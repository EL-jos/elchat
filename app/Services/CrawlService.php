<?php
namespace App\Services;

use App\Models\Site;
use App\Models\CrawlJob;
use App\Models\Page;
use Illuminate\Support\Facades\Http;

class CrawlService
{
    /**
     * Crawl un site et crée les pages / crawl_jobs
     */
    public function crawlSite(Site $site)
    {
        // Pour chaque URL du site (ici, MVP: page racine uniquement ou liste simplifiée)
        $urlsToCrawl = [$site->url];

        foreach ($urlsToCrawl as $url) {
            $crawlJob = CrawlJob::create([
                'site_id' => $site->id,
                'page_url' => $url,
                'status' => 'processing'
            ]);

            try {
                $response = Http::get($url);
                $html = $response->body();
                $text = $this->extractText($html);
                $title = $this->extractTitle($html);

                $page = Page::create([
                    'site_id' => $site->id,
                    'crawl_job_id' => $crawlJob->id,
                    'url' => $url,
                    'title' => $title,
                    'content' => $text
                ]);

                $crawlJob->status = 'done';
                $crawlJob->save();
            } catch (\Exception $e) {
                $crawlJob->status = 'error';
                $crawlJob->error_message = $e->getMessage();
                $crawlJob->save();
            }
        }

        $site->status = 'ready';
        $site->save();
    }

    private function extractText(string $html): string
    {
        // Nettoyage HTML simple MVP
        return strip_tags($html);
    }

    private function extractTitle(string $html): string
    {
        if (preg_match("/<title>(.*?)<\/title>/is", $html, $matches)) {
            return $matches[1];
        }
        return '';
    }
}
