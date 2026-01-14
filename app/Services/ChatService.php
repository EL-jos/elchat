<?php
namespace App\Services;

use App\Models\Site;
use App\Models\Chunk;
use App\Models\Conversation;
use App\Models\Message;

class ChatService
{
    protected $embeddingService;

    public function __construct()
    {
        $this->embeddingService = new EmbeddingService();
    }

    /**
     * Traite la question et retourne la réponse factuelle
     */
    public function ask(string $question, Site $site, ?int $topK = 5, float $similarityThreshold = 0.75)
    {
        // 1. Générer embedding de la question
        $queryEmbedding = $this->embeddingService->getEmbedding($question);

        // 2. Récupérer les chunks du site
        $chunks = Chunk::whereHas('page', fn($q) => $q->where('site_id', $site->id))->get();

        // 3. Calculer similarité cosine (PHP)
        $chunksWithScore = $chunks->map(fn($chunk) => [
            'chunk' => $chunk,
            'score' => $this->cosineSimilarity($queryEmbedding, $chunk->embedding)
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
        // MVP : réponse factuelle placeholder
        if (empty($context)) {
            return "Je ne trouve pas cette information sur ce site.";
        }
        return "Réponse factuelle basée sur le contenu du site.";
    }
}
