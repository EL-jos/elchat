<?php

namespace App\Jobs;

use App\Models\Site;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\BrowserKit\HttpBrowser;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Page;
use App\Services\IndexService;
use Illuminate\Support\Facades\Log;
use Throwable;

class ScrapeSitemapUrlJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public Site $site,
        public string $url
    ) {}

    public function handle(IndexService $indexService): void
    {
        try {
            Log::info("Crawl sitemap page start: {$this->url}");

            $client = new HttpBrowser(HttpClient::create(['timeout' => 60]));
            $client->request('GET', $this->url);
            $crawler = $client->getCrawler();

            // Récupération du body
            $text = $crawler->filter('body')->text('', true);
            $text = preg_replace('/\s+/', ' ', trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

            // Récupération du titre
            $title = $crawler->filter('title')->count()
                ? trim(html_entity_decode($crawler->filter('title')->text(), ENT_QUOTES | ENT_HTML5, 'UTF-8'))
                : '';

            if (strlen($text) < 50) { // ignore les pages trop courtes
                Log::info("Page trop courte, ignorée: {$this->url}");
                return;
            }

            $page = Page::updateOrCreate(
                [
                    'site_id' => $this->site->id,
                    'url' => $this->url,
                ],
                [
                    'crawl_job_id' => null, // pas de crawlJob car sitemap
                    'source' => 'sitemap',
                    'title' => $title,
                    'content' => $text,
                ]
            );

            // Chunk + embeddings
            $indexService->chunkAndIndex($page);

            Log::info("Page sitemap crawlee: {$this->url}");

            $site = $this->site;
            $site->decrement('pending_urls_count');

            if ($site->pending_urls_count <= 0) {
                $site->update([
                    'status' => 'ready',
                    'last_sitemap_crawled_at' => now(),
                ]);
            }

        } catch (\Throwable $e) {
            Log::error("Erreur lors du crawl sitemap page: {$this->url}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function failed(Throwable $exception)
    {
        $this->site->decrement('pending_urls_count');

        if ($this->site->pending_urls_count <= 0) {
            $this->site->update([
                'status' => 'ready',
                'last_sitemap_crawled_at' => now(),
            ]);
        }

        Log::error("Job ScrapeSitemapUrlJob failed for {$this->url}: " . $exception->getMessage());
    }

}
