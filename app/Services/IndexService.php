<?php
namespace App\Services;

use App\Models\Page;
use App\Models\Chunk;

class IndexService
{
    protected $embeddingService;

    public function __construct()
    {
        $this->embeddingService = new EmbeddingService();
    }

    /**
     * Chunk le contenu de la page et génère embeddings
     */
    public function chunkAndIndex(Page $page)
    {
        $chunks = $this->chunkText($page->content, 800); // 500 mots par chunk
        //$chunks = array_unique($chunks);

        foreach ($chunks as $textChunk) {
            $embedding = $this->embeddingService->getEmbedding($textChunk);
            Chunk::create([
                'page_id' => $page->id,
                'text' => $textChunk,
                'embedding' => $embedding
            ]);
        }
    }

    /**
     * Split text en chunks de $chunkSize mots
     */
    /*private function chunkText(string $text, int $chunkSize = 500): array
    {
        $words = preg_split('/\s+/', $text);
        $chunks = array_chunk($words, $chunkSize);
        $chunks = array_unique($chunks);
        return array_map(fn($c) => implode(' ', $c), $chunks);
    }*/
    /*private function chunkText(string $text, int $chunkSize = 500): array
    {
        dd('NOUVELLE VERSION DU CODE');

        return collect(preg_split('/\s+/', $text))
            ->filter()                 // enlève null / ""
            ->chunk($chunkSize)        // Collection de Collections
            ->map(fn ($chunk) => $chunk->implode(' '))
            ->unique()                 // unique SUR LES STRINGS
            ->values()
            ->all();
    }*/

    private function chunkText(string $text, int $chunkSize = 500): array
    {
        return collect(preg_split('/\s+/', $text))
            ->filter(fn ($w) => $w !== '')
            ->chunk($chunkSize)
            ->map(fn ($chunk) => $chunk->implode(' '))
            ->unique()
            ->values()
            ->all();
    }

}
