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

        $includePages = $this->site->include_pages ?? [];
        $excludePages = $this->site->exclude_pages ?? [];

        $urls = [];

        foreach ($xml->url as $urlNode) {
            $url = (string)$urlNode->loc;

            // 1️⃣ Appliquer include_pages si non vide
            if (!empty($includePages) && !$this->matchesPatterns($url, $includePages)) {
                continue; // ignorer si non inclus
            }

            // 2️⃣ Appliquer exclude_pages si non vide
            if (!empty($excludePages) && $this->matchesPatterns($url, $excludePages)) {
                continue; // ignorer si exclu
            }

            $urls[] = $url;
        }

        $this->site->update(['pending_urls_count' => count($urls)]);

        foreach ($urls as $url) {
            dispatch(new ScrapeSitemapUrlJob(
                site: $this->site,
                url: $url
            ));
        }
    }
    /**
     * Vérifie si une URL correspond à une liste de patterns (wildcards *)
     */
    private function matchesPatterns(string $url, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            // transformer le pattern en regex
            $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#i';
            if (preg_match($regex, $url)) {
                return true;
            }
        }
        return false;
    }
}

