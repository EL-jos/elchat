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

}
