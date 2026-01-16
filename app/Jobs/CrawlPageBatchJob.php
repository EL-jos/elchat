<?php

namespace App\Jobs;

use App\Models\CrawlJob;
use App\Models\Site;
use App\Services\CrawlService;
use App\Services\IndexService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

// CrawlPageBatchJob.php
class CrawlPageBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    protected string $siteId;
    protected array $urls; // tableau d'URLs

    public function __construct(string $siteId, array $urls)
    {
        $this->siteId = $siteId;
        $this->urls = $urls;
    }

    public function handle(CrawlService $crawlService, IndexService $indexService)
    {
        $site = Site::findOrFail($this->siteId);

        foreach ($this->urls as $url) {
            $crawlJob = CrawlJob::where('site_id', $site->id)
                ->where('page_url', $url)
                ->first();

            if (!$crawlJob) continue;

            $crawlJob->update(['status' => 'processing']);

            try {
                $page = $crawlService->crawlSinglePage($site, $url, 0);

                if ($page) {
                    $indexService->chunkAndIndex($page);
                }

                $crawlJob->update(['status' => 'done']);
            } catch (\Throwable $e) {
                $crawlJob->update([
                    'status' => 'error',
                    'error_message' => $e->getMessage(),
                ]);
                Log::error("Erreur crawl page {$url}", ['site_id' => $site->id, 'error' => $e->getMessage()]);
            }
        }

        // Vérifier si le site est terminé
        CheckCrawlCompletionJob::dispatch($site->id);

        Log::info("Batch dispatché pour site {$this->siteId}, pages: " . count($this->urls));
    }

    public function failed(Throwable $e)
    {
        Log::error("CrawlPageBatchJob échoué pour site {$this->siteId}", [
            'error' => $e->getMessage(),
            'urls' => $this->urls,
        ]);
    }
}


