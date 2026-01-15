<?php
namespace App\Services;

use App\Models\Site;
use App\Models\Chunk;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\UnansweredQuestion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatService
{

    public function __construct(
        protected EmbeddingService $embeddingService,
        protected SimilarityService $similarityService
    )
    {}

    /**
     * Traite la question et retourne la réponse factuelle
     */
    public function ask(string $question, Site $site, ?int $topK = 5, float $similarityThreshold = 0.45)
    {
        // 1. Générer embedding de la question
        $queryEmbedding = $this->embeddingService->getEmbedding($question);

        // 2. Récupérer les chunks du site
        $chunks = Chunk::whereHas('page', fn($q) => $q->where('site_id', $site->id))->get();

        // 3. Calculer similarité cosine (PHP)
        $chunksWithScore = $chunks->map(fn($chunk) => [
            'chunk' => $chunk,
            'score' => $this->similarityService->cosine(
                $queryEmbedding,
                $chunk->embedding
            )
        ]);

        // 4. Filtrer par score minimum
        $filtered = $chunksWithScore->filter(fn($c) => $c['score'] >= $similarityThreshold);

        // 5. Top K
        $topChunks = $filtered->sortByDesc('score')->take($topK)->pluck('chunk')->toArray();

        // 6. Construire prompt strict pour LLM
        $context = implode("\n", array_map(fn($c) => $c->text, $topChunks));

        // 7. Appel LLM (ici placeholder pour MVP)
        $answer = $this->callLLM($question, $context);

        return $answer;
    }

    /**
     * Répond à une question avec un style commercial et fluide
     */
    public function answer(Site $site, string $question): string
    {
        // 1️⃣ Embedding de la question
        $questionEmbedding = $this->embeddingService->getEmbedding($question);

        // 2️⃣ Récupérer les chunks du site
        $chunks = Chunk::whereHas('page', fn($q) => $q->where('site_id', $site->id))
            ->get();

        $scored = [];

        foreach ($chunks as $chunk) {
            $score = $this->similarityService->cosine(
                $questionEmbedding,
                $chunk->embedding
            );

            if ($score >= 0.3) { // seuil plus bas pour attraper plus d'infos
                $scored[] = [
                    'text' => $chunk->text,
                    'score' => $score,
                ];
            }
        }

        // 🔹 Log debug
        Log::info('RAG DEBUG', [
            'question' => $question,
            'chunks_count' => $chunks->count(),
            'top_scores' => collect($chunks)->map(fn($c) =>
            $this->similarityService->cosine($questionEmbedding, $c->embedding)
            )->sortDesc()->take(5)->values()
        ]);

        // 3️⃣ Si aucun chunk pertinent → fallback
        if (empty($scored)) {
            UnansweredQuestion::create([
                'site_id' => $site->id,
                'question' => $question,
            ]);

            // On met un contexte générique pour que l'IA crée une réponse persuasive
            $context = "Aucune information exacte n'est disponible sur le site pour cette question.";
        } else {
            // 4️⃣ Trier par score et limiter top 3
            usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
            $context = collect(array_slice($scored, 0, 3))
                ->pluck('text')
                ->implode("\n\n---\n\n");
        }

        // 5️⃣ Appel LLM commercial
        return $this->callLLM($question, $context);
    }

    /**
     * Appel LLM (OpenRouter/OpenAI)
     * Appel LLM pour réponse commerciale fluide
     */
    private function callLLM(string $question, string $context): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'meta-llama/llama-3.1-8b-instruct', // ou gpt-3.5-turbo si préféré
            'messages' => [
                [
                    'role' => 'system',
                    'content' =>
                        "Tu es un commercial expert. Utilise le CONTEXTE fourni pour répondre à la question. "
                        . "Même si le CONTEXTE n'a pas l'information exacte, crée une réponse fluide, persuasive et commerciale."
                ],
                [
                    'role' => 'user',
                    'content' =>
                        "CONTEXTE:\n{$context}\n\nQUESTION:\n{$question}"
                ]
            ],
            'temperature' => 0.7, // plus créatif et commercial
            'max_tokens' => 400,
        ]);

        return $response->json()['choices'][0]['message']['content']
            ?? "Je ne trouve pas cette information sur ce site.";
    }
}
