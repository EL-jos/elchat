<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\CrawlSiteJob;
use App\Models\CrawlJob;
use App\Models\Page;
use App\Models\Site;
use App\Services\CrawlService;
use App\Services\IndexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\FacadesLog;
use Illuminate\Support\Str;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Throwable;

class SiteController extends Controller
{
    protected $crawlService;
    protected $indexService;

    public function __construct(CrawlService $crawlService, IndexService $indexService)
    {
        $this->crawlService = $crawlService;
        $this->indexService = $indexService;
    }

    /**
     * Liste des sites de l'utilisateur
     */
    public function index()
    {
        $accountId = auth()->user()->account_id;
        $sites = Site::where('account_id', $accountId)->get();
        return response()->json($sites);
    }

    /**
     * Créer un nouveau site
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'crawl_depth' => 'nullable|integer|min:1|max:5'
        ]);

        $site = Site::create([
            'account_id' => auth()->user()->account_id,
            'url' => $validated['url'],
            'status' => 'pending',
            'crawl_depth' => $validated['crawl_depth'] ?? 1
        ]);

        return response()->json($site, 201);
    }

    /**
     * Afficher un site spécifique
     */
    public function show($id)
    {
        $site = Site::where('id', $id)
            ->where('account_id', auth()->user()->account_id)
            ->firstOrFail();
        return response()->json($site);
    }

    /**
     * Mettre à jour un site
     */
    public function update(Request $request, $id)
    {
        $site = Site::where('id', $id)
            ->where('account_id', auth()->user()->account_id)
            ->firstOrFail();

        $validated = $request->validate([
            'url' => 'nullable|url',
            'crawl_depth' => 'nullable|integer|min:1|max:5',
            'status' => 'nullable|in:pending,crawling,ready,error'
        ]);

        $site->update($validated);
        return response()->json($site);
    }

    /**
     * Supprimer un site
     */
    public function destroy($id)
    {
        $site = Site::where('id', $id)
            ->where('account_id', auth()->user()->account_id)
            ->firstOrFail();

        $site->delete();
        return response()->json(['message'=>'Site deleted']);
    }

    /**
     * Trigger Crawl + Index pour un site
     */
    public function crawl($id)
    {
        $site = Site::where('id', $id)
            ->where('account_id', auth()->user()->account_id)
            ->firstOrFail();


        // Dispatch le Job en arrière-plan
        CrawlSiteJob::dispatch($site->id);

        return response()->json(['message'=>'Crawl and indexing completed', 'site' => $site]);
    }
    public function crawlSite(Request $request, $id)
    {
        $site = Site::where('id', $id)
            ->where('account_id', auth()->user()->account_id)
            ->firstOrFail();

        // Instanciez le service d'indexation
        $indexService = app(IndexService::class);

        try {
            $site->update(['status' => 'crawling']);

            // --- NOUVELLE LOGIQUE DE CRAWLING ICI ---
            $visited = [];
            $seenInQueue = [];
            $baseUrl = rtrim($site->url, '/') . '/';
            $baseHost = parse_url($baseUrl, PHP_URL_HOST); // Extraire le host pour comparaison

            $queue = [$baseUrl];

            while (!empty($queue)) {
                $url = array_shift($queue);

                if (in_array($url, $visited, true)) {
                    continue;
                }

                $visited[] = $url;

                // Créer le crawlJob pour cette page
                $crawlJob = CrawlJob::create([
                    'id' => (string) Str::uuid(),
                    'site_id' => $site->id,
                    'page_url' => $url,
                    'status' => 'processing',
                ]);

                try {
                    // Initialiser le client HttpBrowser
                    $client = new HttpBrowser(HttpClient::create([
                        'timeout' => 60,
                    ]));

                    // Faire la requête GET
                    $response = $client->request('GET', $url);

                    // Obtenir le Crawler
                    $crawler = $client->getCrawler();

                    // Extraction du contenu textuel
                    $text = $crawler->filter('body')->text('', true); // Récupérer le texte du body
                    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $text = preg_replace('/\s+/', ' ', trim($text));

                    // Extraction du titre
                    $title = '';
                    $titleElement = $crawler->filter('title')->first();
                    if ($titleElement->count() > 0) {
                        $title = trim(html_entity_decode($titleElement->text(), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    }

                    // Extraction des liens
                    $links = [];
                    $crawler->filter('a[href]')->each(function (Crawler $node) use (&$links, $url, $baseHost) {
                        $href = trim($node->attr('href'));

                        if (
                            !$href ||
                            str_starts_with($href, '#') ||
                            preg_match('/^(mailto|tel|javascript|ftp|data):/i', $href)
                        ) {
                            return; // Ignorer ces types de liens
                        }

                        // Résoudre l'URL relative à l'URL absolue de la page courante
                        $absoluteUrl = \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL;
                        $resolvedUrl = new \Symfony\Component\Routing\RequestContext();
                        $resolver = new \Symfony\Component\Routing\Matcher\UrlMatcher(new \Symfony\Component\Routing\RouteCollection(), $resolvedUrl);
                        // Ce n'est pas la bonne façon avec HttpBrowser. Utilisons plutôt une fonction utilitaire.
                        $absoluteLink = $this->resolveUrl($href, $url);

                        if ($absoluteLink) {
                            $linkHost = parse_url($absoluteLink, PHP_URL_HOST);
                            if ($linkHost === $baseHost) {
                                $cleanLink = rtrim($absoluteLink, '/');
                                if (!in_array($cleanLink, $links, true)) {
                                    $links[] = $cleanLink;
                                }
                            }
                        }
                    });

                    Log::debug("Page crawlée avec HttpBrowser: {$url}", [
                        'title' => $title,
                        'links_count' => count($links),
                    ]);

                    // Créer l'enregistrement Page
                    Page::create([
                        'id' => (string) Str::uuid(),
                        'site_id' => $site->id,
                        'crawl_job_id' => $crawlJob->id,
                        'url' => $url,
                        'title' => $title,
                        'content' => $text,
                    ]);

                    $crawlJob->update(['status' => 'done']);

                    // Ajouter les nouveaux liens à la file d'attente
                    foreach ($links as $link) {
                        if (!in_array($link, $visited, true) && !in_array($link, $seenInQueue, true)) {
                            $seenInQueue[] = $link;
                            $queue[] = $link;
                        }
                    }

                } catch (Throwable $e) {
                    Log::error("Erreur lors du crawl de {$url}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    $crawlJob->update([
                        'status' => 'error',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }

            // --- FIN DE LA NOUVELLE LOGIQUE DE CRAWLING ---

            // Lancer l'indexation SYNCHRONEMENT
            foreach ($site->pages as $page) {
                $indexService->chunkAndIndex($page);
            }

            $site->update(['status' => 'ready']);

            return response()->json(['message' => 'Crawl and indexing completed', 'site' => $site]);

        } catch (Throwable $e) {
            $site->update(['status' => 'error']);
            Log::error("Erreur globale lors du crawl synchrone du site ID {$site->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Crawl and indexing failed',
                'error' => $e->getMessage(),
                'site' => $site
            ], 500);
        }
    }

    /**
     * Résout une URL relative à partir d'une URL de base.
     * Remplace la logique de UriHttp::resolve.
     *
     * @param string $relativeUrl
     * @param string $baseUrl
     * @return string|null
     */
    private function resolveUrl(string $relativeUrl, string $baseUrl): ?string
    {
        // Si l'URL est déjà absolue
        if (parse_url($relativeUrl, PHP_URL_SCHEME)) {
            return $relativeUrl;
        }

        // Si c'est un chemin absolu (/path)
        if (str_starts_with($relativeUrl, '/')) {
            $baseComponents = parse_url($baseUrl);
            if ($baseComponents === false) {
                return null; // URL de base invalide
            }
            $scheme = $baseComponents['scheme'] ?? 'http';
            $host = $baseComponents['host'] ?? '';
            $port = isset($baseComponents['port']) ? ':' . $baseComponents['port'] : '';
            return $scheme . '://' . $host . $port . $relativeUrl;
        }

        // Sinon, c'est un chemin relatif (./path, ../path, path)
        // Simplifié : on suppose que baseUrl se termine par / ou contient un fichier
        $basePath = dirname(parse_url($baseUrl, PHP_URL_PATH) ?: '/');
        if ($basePath === '.') {
            $basePath = '/';
        }
        $newPath = $basePath . '/' . $relativeUrl;
        // Nettoyer les /./ et /../
        $newPath = $this->normalizePath($newPath);

        $baseComponents = parse_url($baseUrl);
        if ($baseComponents === false) {
            return null; // URL de base invalide
        }
        $scheme = $baseComponents['scheme'] ?? 'http';
        $host = $baseComponents['host'] ?? '';
        $port = isset($baseComponents['port']) ? ':' . $baseComponents['port'] : '';
        return $scheme . '://' . $host . $port . $newPath;
    }

    /**
     * Normalise un chemin en résolvant ./ et ../
     */
    private function normalizePath(string $path): string
    {
        $parts = explode('/', $path);
        $normalized = [];

        foreach ($parts as $part) {
            if ($part === '.' || $part === '') {
                continue;
            }
            if ($part === '..') {
                array_pop($normalized); // Remonte d'un niveau
            } else {
                $normalized[] = $part;
            }
        }

        $result = implode('/', $normalized);
        if (str_starts_with($path, '/')) {
            $result = '/' . $result;
        }
        if (substr($path, -1) === '/' && substr($result, -1) !== '/') {
            $result .= '/';
        }
        return $result;
    }
}
