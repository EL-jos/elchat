<?php
namespace App\Services;

use App\Models\Document;
use App\Models\Page;
use App\Models\Chunk;
use App\Models\Site;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use Spatie\PdfToText\Pdf;
use Throwable;

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

        foreach ($chunks as $textChunk) {
            $embedding = $this->embeddingService->getEmbedding($textChunk);
            Chunk::create([
                'page_id' => $page->id,
                'site_id' => $page->site_id,
                'source_type' => 'page',
                'text' => $textChunk,
                'embedding' => $embedding,
                'priority' => 4, // priorité pour les pages
            ]);
        }
    }
    /**
     * Split text en chunks de $chunkSize mots
     */
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
