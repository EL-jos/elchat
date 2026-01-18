<?php
namespace App\Services;

use App\Models\Document;
use App\Models\Page;
use App\Models\Chunk;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use Spatie\PdfToText\Pdf;
use Throwable;

class IndexService
{
    public function __construct(
        protected EmbeddingService $embeddingService
    ) {}

    /**
     * Point d'entrée UNIQUE
     */
    public function indexPage(Page $page, array $context = []): void
    {
        // 🛑 Idempotence
        if ($page->is_indexed) {
            return;
        }

        DB::beginTransaction();

        try {
            $chunks = $this->buildChunks($page);

            foreach ($chunks as $priority => $textChunk) {
                if ($this->chunkAlreadyExists($page, $textChunk)) {
                    continue;
                }

                $embedding = $this->embeddingService->getEmbedding($textChunk);

                Chunk::create([
                    'page_id'     => $page->id,
                    'site_id'     => $page->site_id,
                    'source_type' => $context['source'] ?? $page->source ?? 'unknown',
                    'text'        => $textChunk,
                    'embedding'   => $embedding,
                    'priority'    => $priority + 1,
                    'document_id' => null,
                ]);
            }

            // ✅ Page marquée indexée SEULEMENT si tout est OK
            $page->update(['is_indexed' => true]);

            DB::commit();

            Log::info('Page indexée', [
                'site_id' => $page->site_id,
                'page_id' => $page->id,
                'chunks' => count($chunks),
            ]);


        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Indexation échouée', [
                'page_id' => $page->id,
                'url' => $page->url,
                'error' => $e->getMessage(),
            ]);

            throw $e; // laisse le job gérer le retry
        }
    }

    /**
     * Construction des chunks
     */
    protected function buildChunks(
        Page $page,
        int $chunkSize = 500,
        float $overlapRatio = 0.15
    ): array {
        return $this->chunkText($page->content, $chunkSize, $overlapRatio);
    }

    /**
     * Déduplication par hash
     */
    protected function chunkAlreadyExists(Page $page, string $text): bool
    {
        $hash = sha1($text);

        return Chunk::where('site_id', $page->site_id)
            ->whereRaw('SHA1(text) = ?', [$hash])
            ->exists();
    }

    /**
     * Découpe avec overlap
     */
    protected function chunkText(
        string $text,
        int $chunkSize,
        float $overlapRatio
    ): array {
        $words = preg_split('/\s+/', trim($text));
        $words = array_values(array_filter($words));
        $chunks = [];

        $overlap = (int) round($chunkSize * $overlapRatio);
        $step = max(1, $chunkSize - $overlap);

        for ($i = 0; $i < count($words); $i += $step) {
            $chunkWords = array_slice($words, $i, $chunkSize);
            if ($chunkWords) {
                $chunks[] = implode(' ', $chunkWords);
            }
        }

        return $chunks;
    }
}

