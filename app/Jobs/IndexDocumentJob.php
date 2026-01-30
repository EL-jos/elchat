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

        if (in_array($this->document->extension, ['csv','xls','xlsx'])) {
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

