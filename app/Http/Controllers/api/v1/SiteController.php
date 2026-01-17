<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\CrawlSiteJob;
use App\Models\CrawlJob;
use App\Models\Document;
use App\Models\Page;
use App\Models\Site;
use App\Services\CrawlService;
use App\Services\IndexService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            'type_site_id' => 'required|exists:type_sites,id',
            'company_name' => 'nullable|string|max:255',
            'crawl_depth' => 'nullable|integer|min:1|max:5',
            'exclude_pages' => 'nullable|array',
            'exclude_pages.*' => 'string',
            'include_pages' => 'nullable|array',
            'include_pages.*' => 'string',
        ]);

        $site = Site::create([
            'account_id' => auth()->user()->account_id,
            'type_site_id' => $validated['type_site_id'],
            'company_name' => $validated['company_name'] ?? null,
            'url' => $validated['url'],
            'status' => 'pending',
            'crawl_depth' => $validated['crawl_depth'] ?? 1,
            'crawl_delay' => $validated['crawl_delay'] ?? 0,
            'exclude_pages' => $validated['exclude_pages'] ?? [],
            'include_pages' => $validated['include_pages'] ?? null,
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
            'url' => 'required|url',
            'crawl_depth' => 'nullable|integer|min:1|max:5',
            'status' => 'nullable|in:pending,crawling,ready,error',
            'type_site_id' => 'required|exists:type_sites,id',
            'company_name' => 'nullable|string|max:255',
            'exclude_pages' => 'nullable|array',
            'exclude_pages.*' => 'string',
            'include_pages' => 'nullable|array',
            'include_pages.*' => 'string',
        ]);

        $site->update([
            'account_id' => auth()->user()->account_id,
            'type_site_id' => $validated['type_site_id'],
            'company_name' => $validated['company_name'],
            'url' => $validated['url'],
            'crawl_depth' => $validated['crawl_depth'] ?? 1,
            'crawl_delay' => $validated['crawl_delay'] ?? 0,
            'exclude_pages' => $validated['exclude_pages'] ?? [],
            'include_pages' => $validated['include_pages'] ?? null,
        ]);
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

        // Mettre directement le status à "crawling" pour l'utilisateur
        $site->update(['status' => 'crawling']);

        return response()->json([
            'message' => 'Crawl started in background',
            'site' => $site
        ]);
    }
}
