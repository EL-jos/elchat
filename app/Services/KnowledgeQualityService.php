<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Chunk;
use App\Models\KnowledgeQualityScore;
use Illuminate\Support\Collection;

class KnowledgeQualityService
{
    public function calculateForSite(Site $site): KnowledgeQualityScore
    {
        $now = now();

        // --- 1️⃣ Total chunks ---
        $totalChunks = Chunk::where('site_id', $site->id)->count();

        // --- 2️⃣ Couverture : % de chunks non vides ---
        $nonEmptyChunks = Chunk::where('site_id', $site->id)
            ->whereRaw('LENGTH(TRIM(text)) > 0')
            ->count();

        $coverageScore = $totalChunks > 0
            ? min(100, ($nonEmptyChunks / $totalChunks) * 100)
            : 0;

        // --- 3️⃣ Qualité des données : longueur moyenne des chunks ---
        $avgLength = Chunk::where('site_id', $site->id)
            ->selectRaw('AVG(LENGTH(TRIM(text))) as avg_length')
            ->value('avg_length') ?? 0;

        $dataQualityScore = min(100, ($avgLength / 500) * 100);

        // --- 4️⃣ Fraîcheur : % de chunks créés ou mis à jour dans les 30 derniers jours ---
        $freshChunks = Chunk::where('site_id', $site->id)
            ->where(function($q) use ($now) {
                $q->where('created_at', '>=', $now->subDays(30))
                    ->orWhere('updated_at', '>=', $now->subDays(30));
            })->count();

        $freshnessScore = $totalChunks > 0
            ? min(100, ($freshChunks / $totalChunks) * 100)
            : 0;

        // --- 5️⃣ Richesse sémantique : diversité des tokens ---
        $uniqueTokens = collect();
        $totalTokens = 0;

        Chunk::where('site_id', $site->id)
            ->select('text')
            ->chunk(500, function ($chunks) use (&$uniqueTokens, &$totalTokens) {
                foreach ($chunks as $c) {
                    $tokens = preg_split('/\s+/', strtolower($c->text));
                    $tokens = array_filter($tokens, fn($t) => strlen($t) > 0);
                    $uniqueTokens = $uniqueTokens->merge($tokens);
                    $totalTokens += count($tokens);
                }
            });

        $semanticScore = $totalTokens > 0
            ? min(100, ($uniqueTokens->unique()->count() / $totalTokens) * 100)
            : 0;

        // --- 6️⃣ Score global pondéré ---
        $globalScore = ($coverageScore * 0.3)
            + ($dataQualityScore * 0.3)
            + ($semanticScore * 0.2)
            + ($freshnessScore * 0.2);

        // --- 7️⃣ Enregistrement ou mise à jour ---
        return KnowledgeQualityScore::updateOrCreate(
            ['site_id' => $site->id, 'scope_type' => 'global'],
            [
                'coverage_score'     => round($coverageScore, 2),
                'data_quality_score' => round($dataQualityScore, 2),
                'semantic_score'     => round($semanticScore, 2),
                'freshness_score'    => round($freshnessScore, 2),
                'global_score'       => round($globalScore, 2),
            ]
        );
    }
}

