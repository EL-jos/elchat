<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\Site;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessSitemapJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public Site $site,
        public Document $sitemapPath
    ) {}

    public function handle()
    {
        $xml = simplexml_load_file(public_path($this->sitemapPath->path));

        if (!$xml || !isset($xml->url)) {
            Log::warning("Sitemap vide ou mal formé: {$this->sitemapPath->path}");
            return;
        }

        $urls = [];
        foreach ($xml->url as $urlNode) {
            $urls[] = (string)$urlNode->loc;
        }

        // Mettre à jour le compteur
        $this->site->update(['pending_urls_count' => count($urls)]);

        foreach ($xml->url as $urlNode) {
            $url = (string) $urlNode->loc;

            dispatch(new ScrapeSitemapUrlJob(
                site: $this->site,
                url: $url
            ));
        }
    }
}

