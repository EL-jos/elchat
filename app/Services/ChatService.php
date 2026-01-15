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

    public function answer(Site $site, string $question): string
    {
        // 1. Embedding de la question
        $questionEmbedding = $this->embeddingService->getEmbedding($question);

        // 2. Charger les chunks du site
        $chunks = Chunk::whereHas('page', fn($q) =>
        $q->where('site_id', $site->id)
        )->get();

        $scored = [];

        foreach ($chunks as $chunk) {
            $score = $this->similarityService->cosine(
                $questionEmbedding,
                $chunk->embedding
            );

            if ($score >= 0.55) {
                $scored[] = [
                    'text' => $chunk->text,
                    'score' => $score,
                ];
            }
        }

        Log::info('RAG DEBUG', [
            'question' => $question,
            'chunks_count' => $chunks->count(),
            'scores' => collect($chunks)->map(fn($c) =>
            $this->similarityService->cosine(
                $questionEmbedding,
                $c->embedding
            )
            )->sortDesc()->take(5)->values()
        ]);


        // 3. Aucun contexte → question sans réponse
        if (empty($scored)) {
            UnansweredQuestion::create([
                'site_id' => $site->id,
                'question' => $question,
            ]);

            return "Je ne trouve pas cette information sur ce site.";
        }

        // 4. Trier par score et limiter
        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        $context = collect(array_slice($scored, 0, 3))
            ->pluck('text')
            ->implode("\n\n---\n\n");

        //dump($context);

        // 5. Appel LLM
        return $this->callLLM($question, $context);
    }

    /**
     * Cosine similarity
     */
    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dot = 0; $normA = 0; $normB = 0;
        foreach ($vecA as $i => $val) {
            $dot += $val * ($vecB[$i] ?? 0);
            $normA += $val ** 2;
            $normB += ($vecB[$i] ?? 0) ** 2;
        }
        return $normA && $normB ? $dot / (sqrt($normA) * sqrt($normB)) : 0;
    }

    /**
     * Appel LLM (OpenRouter/OpenAI)
     */
    private function callLLM(string $question, string $context): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'meta-llama/llama-3.1-8b-instruct',
            'messages' => [
                [
                    'role' => 'system',
                    'content' =>
                        "Tu réponds uniquement avec le CONTEXTE fourni. "
                        . "Si l'information n'existe pas, dis : "
                        . "'Je ne trouve pas cette information sur ce site.'"
                ],
                [
                    'role' => 'user',
                    'content' =>
                        "CONTEXTE:\n{$context}\n\nQUESTION:\n{$question}"
                ]
            ],
            'temperature' => 0,
            'max_tokens' => 300,
        ]);

        //dd($response->json());
        return $response->json()['choices'][0]['message']['content']
            ?? "Je ne trouve pas cette information sur ce site.";
    }
}
