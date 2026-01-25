<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\CrawlSiteJob;
use App\Models\Chunk;
use App\Models\Conversation;
use App\Models\CrawlJob;
use App\Models\Document;
use App\Models\Message;
use App\Models\Page;
use App\Models\Site;
use App\Services\CrawlService;
use App\Services\IndexService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
        $accountId = auth()->user()->ownedAccount->id;
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
            'type_site_id' => 'required|exists:type_sites,id',
            'name' => 'required|string|max:255',
            'crawl_depth' => 'nullable|integer|min:1|max:5',
            'exclude_pages' => 'nullable|array',
            'exclude_pages.*' => 'string',
            'include_pages' => 'nullable|array',
            'include_pages.*' => 'string',
        ]);

        $site = Site::create([
            'account_id' => auth()->user()->ownedAccount->id,
            'type_site_id' => $validated['type_site_id'],
            'name' => $validated['name'] ?? null,
            'url' => $validated['url'],
            'status' => 'pending',
            'crawl_depth' => $validated['crawl_depth'] ?? 1,
            'crawl_delay' => $validated['crawl_delay'] ?? 0,
            'exclude_pages' => $validated['exclude_pages'] ?? [],
            'include_pages' => $validated['include_pages'] ?? null,
            'favicon' => $this->getGoogleFaviconSecure($validated['url']),
        ]);

        return response()->json($site->load('type'), 201);
    }
    /**
     * Afficher un site spécifique
     */
    public function show($id)
    {
        $site = Site::where('id', $id)
            ->where('account_id', auth()->user()->ownedAccount->id)
            ->firstOrFail();
        return response()->json($site);
    }
    /**
     * Mettre à jour un site
     */
    public function update(Request $request, $id)
    {
        $site = Site::where('id', $id)
            ->where('account_id', auth()->user()->ownedAccount->id)
            ->firstOrFail();

        $validated = $request->validate([
            'url' => 'required|url',
            'crawl_depth' => 'nullable|integer|min:1|max:5',
            'status' => 'nullable|in:pending,crawling,ready,error',
            'type_site_id' => 'required|exists:type_sites,id',
            'name' => 'required|string|max:255',
            'exclude_pages' => 'nullable|array',
            'exclude_pages.*' => 'string',
            'include_pages' => 'nullable|array',
            'include_pages.*' => 'string',
        ]);

        $site->update([
            //'account_id' => auth()->user()->ownedAccount->id,
            'type_site_id' => $validated['type_site_id'],
            'name' => $validated['name'],
            'url' => $validated['url'],
            'crawl_depth' => $validated['crawl_depth'] ?? 1,
            'crawl_delay' => $validated['crawl_delay'] ?? 0,
            'exclude_pages' => $validated['exclude_pages'] ?? [],
            'include_pages' => $validated['include_pages'] ?? null,
            'favicon' => $this->getGoogleFaviconSecure($validated['url']),
        ]);
        return response()->json($site->load('type'));
    }
    /**
     * Supprimer un site
     */
    public function destroy($id)
    {
        $site = Site::where('id', $id)
            ->where('account_id', auth()->user()->ownedAccount->id)
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
            ->where('account_id', auth()->user()->ownedAccount->id)
            ->firstOrFail();

        // Dispatch le Job en arrière-plan
        CrawlSiteJob::dispatch($site->id);

        // Mettre directement le status à "crawling" pour l'utilisateur
        $site->update(['status' => 'crawling']);

        return response()->json([
            'message' => 'Crawl started in background',
            'site' => $site
        ]);
    }

    private function getGoogleFaviconSecure(
        string $url,
        int $size = 64,
        bool $removeWww = true
    ): ?string {

        // Tailles autorisées par Google
        $allowedSizes = [16, 32, 48, 64, 128, 256];

        if (!in_array($size, $allowedSizes, true)) {
            $size = 64; // fallback sécurisé
        }

        // Nettoyage de l'URL
        $url = trim($url);

        // Ajouter un schéma si absent (obligatoire pour parse_url)
        if (!preg_match('~^https?://~i', $url)) {
            $url = 'https://' . $url;
        }

        $parts = parse_url($url);

        if (empty($parts['host'])) {
            return null;
        }

        $domain = strtolower($parts['host']);

        // Supprimer www. si demandé
        if ($removeWww) {
            $domain = preg_replace('/^www\./i', '', $domain);
        }

        // Validation stricte du domaine
        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return null;
        }

        // Construction de l'URL finale
        return sprintf(
            'https://www.google.com/s2/favicons?sz=%d&domain=%s',
            $size,
            $domain
        );
    }

    public function siteChunks(Request $request, string $siteId)
    {
        $site = Site::findOrFail($siteId);
        $user = auth()->user();
        $account = $user->ownedAccount;

        abort_if($site->account_id !== $account->id, 403);

        $perPage = $request->query('per_page', 20); // par défaut 20
        $page = $request->query('page', 1);

        $documentIds = Document::where('documentable_id', $site->id)
            ->where('documentable_type', Site::class)
            ->pluck('id');

        $pageIds = Page::where('site_id', $site->id)->pluck('id');

        $chunksQuery = Chunk::where(function ($q) use ($documentIds, $pageIds) {
            $q->whereIn('document_id', $documentIds)
                ->orWhereIn('page_id', $pageIds);
        });

        $chunks = $chunksQuery->paginate($perPage, ['*'], 'page', $page);

        return response()->json($chunks);
    }

    public function pagesOverview(string $siteId)
    {
        /** @var Site $site */
        $site = Site::where('id', $siteId)
            ->where('account_id', auth()->user()->ownedAccount->id)
            ->firstOrFail();

        /**
         * 📊 STATS
         */
        $totalPages = Page::where('site_id', $site->id)->count();

        $indexedPages = Page::where('site_id', $site->id)
            ->where('is_indexed', true)
            ->count();

        $errorPages = Page::where('site_id', $site->id)
            ->whereHas('crawlJob', function ($q) {
                $q->where('status', 'error');
            })
            ->count();

        $excludedPages = Page::where('site_id', $site->id)
            ->where('is_indexed', false)
            ->whereDoesntHave('crawlJob', function ($q) {
                $q->where('status', 'error');
            })
            ->count();

        $totalChunks = Chunk::where('site_id', $site->id)->count();

        /**
         * 📄 PAGES LIST
         */
        $pages = Page::where('site_id', $site->id)
            ->with([
                'crawlJob:id,status,error_message,created_at'
            ])
            ->withCount('chunks')
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (Page $page) {
                return [
                    'id' => $page->id,
                    'site_id' => $page->site_id,
                    'url' => $page->url,
                    'title' => $page->title,
                    'source' => $page->source, // crawl | sitemap
                    'is_indexed' => $page->is_indexed,
                    'chunks_count' => $page->chunks_count,
                    'last_crawl' => $page->crawlJob ? [
                        'id' => $page->crawlJob->id,
                        'status' => $page->crawlJob->status,
                        'error_message' => $page->crawlJob->error_message,
                        'created_at' => $page->crawlJob->created_at?->toISOString(),
                    ] : null,
                    'created_at' => $page->created_at->toISOString(),
                    'updated_at' => $page->updated_at->toISOString(),
                ];
            });

        /**
         * ✅ RESPONSE
         */
        return response()->json([
            'site' => [
                'id' => $site->id,
                'name' => $site->name,
                'url' => $site->url,
                'status' => $site->status,
                'favicon' => $site->favicon
            ],
            'stats' => [
                'total_pages' => $totalPages,
                'indexed_pages' => $indexedPages,
                'excluded_pages' => $excludedPages,
                'error_pages' => $errorPages,
                'total_chunks' => $totalChunks,
            ],
            'pages' => $pages,
        ]);
    }

    /*public function widgetTest(Request $request, string $siteId)
    {
        $site = Site::findOrFail($siteId);
        $user = $request->user();

        // 🔐 Vérification que l'utilisateur est bien propriétaire du site
        abort_if($site->account_id !== $user->ownedAccount->id, 403);

        // 🔹 URL à tester
        //$url = rtrim($site->url, '/') . '/'; // s'assure que la racine est correcte
        $url = "http://127.0.0.1:5500/index.html";

        // 🔹 Tag attendu (adaptable si plusieurs types)
        $widgetTagPattern = sprintf(
            '/<script\s+async\s+src="https:\/\/www\.domain\.com\/elchat\/js\?id=%s"><\/script>/',
            preg_quote($site->id, '/')
        );

        try {
            // -------------------------
            // 🔹 Requête HTTP vers la page du site
            // -------------------------
            $response = Http::timeout(5)
                ->followRedirects()
                ->get($url);

            $body = $response->body();

            // -------------------------
            // 🔹 Vérification du tag ELChat
            // -------------------------
            if (preg_match($widgetTagPattern, $body)) {
                $status = 'ok';
                $message = 'Le widget ELChat est détecté et semble fonctionnel';
                $detected_tag = true;
            } else {
                $status = 'error';
                $message = 'Le widget ELChat n\'est pas trouvé sur la page';
                $detected_tag = false;
            }

        } catch (\Exception $e) {
            $status = 'error';
            $message = 'Impossible d\'atteindre le site ou de tester le widget: ' . $e->getMessage();
            $detected_tag = false;
        }

        // -------------------------
        // 🔹 Réponse structurée
        // -------------------------
        return response()->json([
            'site_id' => $site->id,
            'site_url' => $site->url,
            'status' => $status,
            'message' => $message,
            'detected_tag' => $detected_tag,
            'tested_at' => now()->toISOString(),
            'widget_expected_src' => "https://www.domain.com/elchat/js?id={$site->id}",
        ]);
    }*/
    public function widgetTest(Request $request, string $siteId)
    {
        $site = Site::findOrFail($siteId);
        $user = $request->user();

        abort_if($site->account_id !== $user->ownedAccount->id, 403);

        // 🔹 URL à tester
        // Test local ou prod
        $url = "http://127.0.0.1:5500"; // pour le test local
        // $url = rtrim($site->url, '/') . '/'; // pour prod

        // 🔹 Tag attendu
        $widgetTagPattern = sprintf(
            '/<script\s+async\s+src="https:\/\/www\.domain\.com\/elchat\/js\?id=%s"><\/script>/',
            preg_quote($site->id, '/')
        );

        try {
            $response = Http::timeout(5)->get($url); // plus de followRedirects
            $body = $response->body();

            if (preg_match($widgetTagPattern, $body)) {
                $status = 'ok';
                $message = 'Le widget ELChat est détecté et semble fonctionnel';
                $detected_tag = true;
            } else {
                $status = 'error';
                $message = 'Le widget ELChat n\'est pas trouvé sur la page';
                $detected_tag = false;
            }

        } catch (\Exception $e) {
            $status = 'error';
            $message = 'Impossible d\'atteindre le site ou de tester le widget: ' . $e->getMessage();
            $detected_tag = false;
        }

        return response()->json([
            'site_id' => $site->id,
            'site_url' => $site->url,
            'status' => $status,
            'message' => $message,
            'detected_tag' => $detected_tag,
            'tested_at' => now()->toISOString(),
            'widget_expected_src' => "https://www.domain.com/elchat/js?id={$site->id}",
        ]);
    }

}
