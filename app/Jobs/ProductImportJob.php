<?php

namespace App\Jobs;

use App\Mappers\ProductFileParser;
use App\Mappers\ProductMapper;
use App\Models\Document;
use App\Models\ProductImport;
use App\Models\Site;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProductImportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Document $document,
        public $mapping,
        public Site $site
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $productsRaw = ProductFileParser::parse($this->document);

        $products = ProductMapper::map($productsRaw, $this->mapping);

        $existing = ProductImport::where('document_id', $this->document->id)
            ->where('status', 'processing')
            ->first();

        if ($existing) {
            return;
        }

        $import = ProductImport::create([
            'site_id' => $this->site->id,
            'document_id' => $this->document->id,
            'total_products' => count($products),
            'processed_products' => 0,
            'status' => 'processing',
            'started_at' => now()
        ]);

        collect($products)
            ->chunk(100) // 🔥 batch size configurable
            ->each(function ($batch) use ($import) {
                IndexProductBatchJob::dispatch(
                    $batch,
                    $this->document,
                    $import->id
                );
            });

        CheckProductImportCompletionJob::dispatch($import->id)->delay(now()->addMinutes(1));

        $this->site->update(['status' => 'indexing']);

    }
}
