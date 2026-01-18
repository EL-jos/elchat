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
    protected function chunkText( string $text, int $chunkSize, float $overlapRatio ): array {
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

    /**
     * For Document
     */

    public function indexDocument(Document $document, array $context = []): void
    {
        // 1️⃣ Extraction du texte
        $text = $this->extractTextFromDocument($document->path, $document->type);

        if (strlen($text) < 50) {
            Log::info("Document trop court, ignoré: {$document->path}");
            return;
        }

        DB::beginTransaction();

        try {
            $chunks = $this->chunkText($text, 500, 0.15);

            foreach ($chunks as $priority => $textChunk) {
                if ($this->chunkAlreadyExistsForDocument($document, $textChunk)) {
                    continue;
                }

                $embedding = $this->embeddingService->getEmbedding($textChunk);

                Chunk::create([
                    'page_id'     => null, // pas lié à une page
                    'site_id'     => $document->documentable->site->id ?? null,
                    'document_id' => $document->id,
                    'source_type' => 'document',
                    'text'        => $textChunk,
                    'embedding'   => $embedding,
                    'priority'    => $priority + 1,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Indexation document échouée: {$document->path}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    protected function chunkAlreadyExistsForDocument(Document $document, string $text): bool
    {
        $hash = sha1($text);

        return Chunk::where('document_id', $document->id)
            ->whereRaw('SHA1(text) = ?', [$hash])
            ->exists();
    }

    /**
     * Extraction du texte selon type
     */
    protected function extractTextFromDocument(string $path, string $type): string
    {
        $fullPath = public_path($path);

        return match($type) {
            'pdf' => $this->extractTextFromPDF($fullPath),
            'doc', 'docx' => $this->extractTextFromWord($fullPath),
            'txt' => file_get_contents($fullPath),
            'csv' => $this->extractTextFromCSV($fullPath),
            'xls', 'xlsx' => $this->extractTextFromExcel($fullPath),
            default => '',
        };
    }

    protected function extractTextFromPDF(string $fullPath): string
    {
        if (!file_exists($fullPath)) return '';

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($fullPath);
            return trim($pdf->getText());
        } catch (\Throwable $e) {
            Log::error("Erreur extraction PDF: {$fullPath}", ['error' => $e->getMessage()]);
            return '';
        }
    }
    protected function extractTextFromWord(string $fullPath): string
    {
        if (!file_exists($fullPath)) return '';

        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($fullPath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . ' ';
                    }
                }
            }

            return trim($text);
        } catch (\Throwable $e) {
            Log::error("Erreur extraction Word: {$fullPath}", ['error' => $e->getMessage()]);
            return '';
        }
    }
    protected function extractTextFromExcel(string $fullPath): string
    {
        if (!file_exists($fullPath)) return '';

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
            $text = '';

            foreach ($spreadsheet->getAllSheets() as $sheet) {
                foreach ($sheet->toArray() as $row) {
                    $text .= implode(' ', $row) . ' ';
                }
            }

            return trim($text);
        } catch (\Throwable $e) {
            Log::error("Erreur extraction Excel: {$fullPath}", ['error' => $e->getMessage()]);
            return '';
        }
    }
    protected function extractTextFromCSV(string $fullPath): string
    {
        if (!file_exists($fullPath)) return '';

        $text = '';
        try {
            if (($handle = fopen($fullPath, 'r')) !== false) {
                while (($data = fgetcsv($handle, 0, ",")) !== false) {
                    $text .= implode(' ', $data) . ' ';
                }
                fclose($handle);
            }
        } catch (\Throwable $e) {
            Log::error("Erreur extraction CSV: {$fullPath}", ['error' => $e->getMessage()]);
        }

        return trim($text);
    }
    protected function extractTextFromTXT(string $fullPath): string
    {
        if (!file_exists($fullPath)) return '';
        return trim(file_get_contents($fullPath));
    }

    /**
     * Indexe un document WooCommerce CSV/Excel
     */
    public function indexWooCommerceDocument(Document $document): void
    {
        $fullPath = public_path($document->path);
        $type = strtolower($document->type);
        $type = "csv";

        $products = $this->parseWooCommerceFile($fullPath, $type);

        //dd($document->path, $fullPath, $type,$products);

        if (empty($products)) {
            Log::info("Aucun produit trouvé dans le document: {$document->path}");
            return;
        }

        foreach ($products as $priority => $product) {
            try {

                $sku = $product['sku'] ?? null;

                // 1️⃣ Chunk global
                $parts = [];
                foreach ($product as $key => $value) {
                    if ($value) $parts[] = ucfirst(str_replace('_',' ',$key)) . ": " . $value;
                }
                $textGlobal = implode(". ", $parts) . ".";

                $embeddingGlobal = $this->embeddingService->getEmbedding($textGlobal);
                $titleEmbedding = !empty($product['post_title'])
                    ? $this->embeddingService->getEmbedding($product['post_title'])
                    : null;

                $priceText = (!empty($product['regular_price']) || !empty($product['sale_price']))
                    ? 'Prix régulier: ' . ($product['regular_price'] ?? '') .
                    ', Prix promo: ' . ($product['sale_price'] ?? '')
                    : null;

                $priceEmbedding = $priceText
                    ? $this->embeddingService->getEmbedding($priceText)
                    : null;

                DB::beginTransaction();
                if ($this->chunkAlreadyExistsForDocument($document, $textGlobal)) {
                    DB::rollBack();
                    continue;
                }


                Chunk::create([
                    'page_id'     => null,
                    'site_id'     => $document->documentable->id,
                    'document_id' => $document->id,
                    'source_type' => 'woocommerce',
                    'text'        => $textGlobal,
                    'embedding'   => $embeddingGlobal,
                    'priority'    => $priority + 1,
                    'metadata'    => $product,
                ]);

                // 2️⃣ Chunks spécifiques pour infos clés
                if (!empty($product['post_title'])) {
                    Chunk::create([
                        'page_id'     => null,
                        'site_id'     => $document->documentable->id,
                        'document_id' => $document->id,
                        'source_type' => 'woocommerce',
                        'text'        => 'Titre: ' . $product['post_title'],
                        'embedding'   => $titleEmbedding,
                        'priority'    => $priority + 1,
                        'metadata'    => ['type' => 'title', 'sku' => $sku],
                    ]);
                }

                if (!empty($product['regular_price']) || !empty($product['sale_price'])) {

                    Chunk::create([
                        'page_id'     => null,
                        'site_id'     => $document->documentable->id,
                        'document_id' => $document->id,
                        'source_type' => 'woocommerce',
                        'text'        => $priceText,
                        'embedding'   => $priceEmbedding,
                        'priority'    => $priority + 1,
                        'metadata'    => ['type' => 'price', 'sku' => $sku],
                    ]);
                }

                DB::commit();
                //Log::info("Document WooCommerce indexé avec " . count($products) . " produits: {$document->path}");
                Log::info("Produit WooCommerce indexé", ['sku' => $sku]);

            } catch (Throwable $e) {
                DB::rollBack();
                Log::error("Indexation document WooCommerce échouée: {$document->path}", [
                    'error' => $e->getMessage()
                ]);
                continue;
                //throw $e;
            }
        }


    }

    /**
     * Parse CSV / Excel WooCommerce
     */
    protected function parseWooCommerceFile(string $fullPath, string $type): array
    {
        if (!file_exists($fullPath)) return [];

        $products = [];

        try {
            if (in_array($type, ['xls','xlsx'])) {
                $spreadsheet = IOFactory::load($fullPath);
                foreach ($spreadsheet->getAllSheets() as $sheet) {
                    $rows = $sheet->toArray();
                    $headers = array_map('strtolower', array_shift($rows));
                    foreach ($rows as $row) {
                        $products[] = array_combine($headers, $row);
                    }
                }
            } elseif ($type === 'csv') {
                if (($handle = fopen($fullPath, 'r')) !== false) {
                    $headers = fgetcsv($handle, 0, ",");
                    $headers = array_map('strtolower', $headers);
                    while (($row = fgetcsv($handle, 0, ",")) !== false) {
                        $products[] = array_combine($headers, $row);
                    }
                    fclose($handle);
                }
            }
        } catch (Throwable $e) {
            Log::error("Erreur parsing WooCommerce file: {$fullPath}", ['error' => $e->getMessage()]);
        }

        return $products;
    }

}

