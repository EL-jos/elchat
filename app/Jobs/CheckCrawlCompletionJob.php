<?php

namespace App\Jobs;

use App\Models\CrawlJob;
use App\Models\Page;
use App\Models\Site;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// CheckCrawlCompletionJob.php
class CheckCrawlCompletionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $siteId;
    public $timeout = 60;
    public $tries = 3;

    public function __construct(string $siteId)
    {
        $this->siteId = $siteId;
    }

    public function handle()
    {
        $site = Site::find($this->siteId);
        if (!$site) return;

        $pendingJobs = CrawlJob::where('site_id', $site->id)
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        if ($pendingJobs === 0) {
            $site->update(['status' => 'ready']);
            Log::info("Crawl terminé pour le site {$site->url} ✅");
        } else {
            self::dispatch($this->siteId)->delay(now()->addSeconds(10));
        }
    }
}


