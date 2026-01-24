<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Site;
use App\Models\Page;
use App\Models\Document;
use App\Models\Chunk;
use App\Models\Conversation;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function overview(Request $request)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6);

        $account = auth()->user()->ownedAccount;
        if (!$account) {
            return response()->json(['error' => 'No owned account'], 404);
        }

        $sites = Site::where('account_id', $account->id)->with('type')->get();
        $siteIds = $sites->pluck('id');

        //dd($sites->);

        // =====================
        // 🔢 TOTAUX GLOBAUX
        // =====================
        $total_sites = $sites->count();

        $total_documents = Document::whereIn('documentable_id', $siteIds)
            ->where('documentable_type', Site::class)
            ->count();

        $conversationIds = Conversation::whereIn('site_id', $siteIds)->pluck('id');

        $total_conversations = $conversationIds->count();
        $total_messages = Message::whereIn('conversation_id', $conversationIds)->count();

        // 🔹 Nombre total d’utilisateurs liés aux sites
        $total_users = DB::table('site_user')
            ->whereIn('site_id', $siteIds)
            ->distinct('user_id')
            ->count('user_id');

        // =====================
        // 📆 PÉRIODE (7 JOURS)
        // =====================
        $period = collect();
        for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
            $period->push($d->format('Y-m-d'));
        }

        $conversations_per_day = [];
        $messages_per_day = [];
        $source_distribution = [];

        foreach ($sites as $site) {

            // =====================
            // 💬 CONVERSATIONS / SITE
            // =====================
            $siteConversations = Conversation::where('site_id', $site->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->get();

            $siteConversationIds = $siteConversations->pluck('id');

            $siteMessages = Message::whereIn('conversation_id', $siteConversationIds)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->get();

            $conversations_per_day[] = [
                'site_name' => $site->name,
                'data' => $period->map(fn ($day) => [
                    'date' => $day,
                    'count' => $siteConversations
                        ->whereBetween('created_at', [$day.' 00:00:00', $day.' 23:59:59'])
                        ->count()
                ])
            ];

            $messages_per_day[] = [
                'site_name' => $site->name,
                'data' => $period->map(fn ($day) => [
                    'date' => $day,
                    'count' => $siteMessages
                        ->whereBetween('created_at', [$day.' 00:00:00', $day.' 23:59:59'])
                        ->count()
                ])
            ];

            // =====================
            // 📦 SOURCE DISTRIBUTION (CORRECTE)
            // =====================

            // Documents liés au site
            $siteDocumentIds = Document::where('documentable_id', $site->id)
                ->where('documentable_type', Site::class)
                ->pluck('id');

            // Pages du site (pour crawl + sitemap)
            $pageIds = Page::where('site_id', $site->id)->pluck('id');

            $chunks = Chunk::where(function ($q) use ($siteDocumentIds, $pageIds) {
                $q->whereIn('document_id', $siteDocumentIds)
                    ->orWhereIn('page_id', $pageIds);
            })->get();

            $source_distribution[] = [
                'site_name' => $site->name,
                'sources' => [
                    'crawl'       => $chunks->where('source_type', 'crawl')->count(),
                    'woocommerce' => $chunks->where('source_type', 'woocommerce')->count(),
                    'manuel'      => $chunks->where('source_type', 'manuel')->count(),
                    'sitemap'     => $chunks->where('source_type', 'sitemap')->count(),
                ]
            ];
        }

        // 🔹 Liste des sites (type_site, name, url, status)

        /*$sites_list = $sites->map(fn ($site) => [
            'id' => $site->id,
            'type_site' => $site->type,
            'favicon' => $site->favicon,
            'name' => $site->name,
            'url' => $site->url,
            'status' => $site->status,
            'created_at' => Carbon::parse($site->created_at)->format('Y-m-d'),
            'exclude_pages' => $site->exclude_pages,
            'include_pages' => $site->include_pages,
        ]);*/


        return response()->json([
            'total_sites' => $total_sites,
            'total_documents' => $total_documents,
            'total_conversations' => $total_conversations,
            'total_messages' => $total_messages,
            'total_users' => $total_users,
            'sites' => $sites,
            'conversations_per_day' => $conversations_per_day,
            'messages_per_day' => $messages_per_day,
            'source_distribution' => $source_distribution,
        ]);
    }
    public function siteOverview(Request $request, Site $site)
    {
        $user = auth()->user();
        $account = $user->ownedAccount;

        // 🔐 Sécurité
        abort_if($site->account_id !== $account->id, 403);

        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6);

        // -------------------
        // Documents & chunks
        // -------------------

        $documentIds = Document::where('documentable_id', $site->id)
            ->where('documentable_type', Site::class)
            ->pluck('id');

        $chunks = Chunk::where(function ($query) use ($documentIds) {
            $query->whereIn('document_id', $documentIds)
                ->orWhereNull('document_id');
        })->get();

        // -------------------
        // Conversations & messages
        // -------------------

        $conversations = Conversation::where('site_id', $site->id)
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get();

        $conversationIds = $conversations->pluck('id');

        $messages = Message::whereIn('conversation_id', $conversationIds)
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get();

        // -------------------
        // Période (7 jours)
        // -------------------

        $period = collect();
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $period->push($date->format('Y-m-d'));
        }

        // -------------------
        // Groupement par jour
        // -------------------

        $conversations_per_day = $period->map(function ($day) use ($conversations) {
            return [
                'date' => $day,
                'count' => $conversations
                    ->whereBetween('created_at', [$day.' 00:00:00', $day.' 23:59:59'])
                    ->count()
            ];
        });

        $messages_per_day = $period->map(function ($day) use ($messages) {
            return [
                'date' => $day,
                'count' => $messages
                    ->whereBetween('created_at', [$day.' 00:00:00', $day.' 23:59:59'])
                    ->count()
            ];
        });

        // -------------------
        // Source distribution
        // -------------------

        $source_distribution = [
            'pages'       => $chunks->where('source_type', 'crawl')->count(),
            'woocommerce' => $chunks->where('source_type', 'woocommerce')->count(),
            'documents'   => $chunks->where('source_type', 'manuel')->count(),
        ];

        return response()->json([
            'site' => [
                'id' => $site->id,
                'name' => $site->name,
            ],

            'total_documents' => $documentIds->count(),
            'total_chunks' => $chunks->count(),
            'total_conversations' => $conversations->count(),
            'total_messages' => $messages->count(),

            'conversations_per_day' => $conversations_per_day,
            'messages_per_day' => $messages_per_day,
            'source_distribution' => $source_distribution,
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


}
