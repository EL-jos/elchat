<?php
// app/Jobs/CrawlSiteJob.php

namespace App\Jobs;

use App\Models\Site;
use App\Services\CrawlService; // Importer le service de crawling
use App\Services\IndexService; // Importer le service d'indexation
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CrawlSiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0; // PAS de limite spécifique ici, gérée par la queue et le worker
    public $tries = 1;

    protected string $siteId;

    public function __construct(string $siteId)
    {
        $this->siteId = $siteId;
    }

    public function handle(
        CrawlService $crawlService,    // Injection du service de crawling
        IndexService $indexService    // Injection du service d'indexation
    ): void {
        $site = Site::findOrFail($this->siteId);

        // Appeler la méthode du service pour effectuer le crawling
        $crawlService->crawlSite($site);

        // Une fois le crawling terminé, lancer l'indexation
        foreach ($site->pages as $page) {
            $indexService->chunkAndIndex($page);
        }
    }

    public function failed(Throwable $e): void
    {
        $site = Site::find($this->siteId);

        if ($site) {
            $site->update(['status' => 'error']);
            Log::error("Job CrawlSiteJob échoué pour le site ID {$this->siteId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        } else {
            Log::warning("Job CrawlSiteJob échoué : Site ID {$this->siteId} introuvable.");
        }
    }
}
