<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\IndexService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class IndexDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Document $document) {}

    public function handle(IndexService $indexService)
    {
        $type = strtolower($this->document->type);
        $type = "csv";
        //dd("c'est un produit", $type);
        // Si CSV/Excel, on considère WooCommerce
        if (in_array($type, ['csv','xls','xlsx'])) {
            $indexService->indexWooCommerceDocument($this->document);
        } else {
            $indexService->indexDocument($this->document);
        }

        Log::info("Document indexé: {$this->document->path}");
    }

    public function failed(Throwable $e)
    {
        Log::error("IndexDocumentJob failed: {$this->document->path}", [
            'error' => $e->getMessage()
        ]);
    }
}

