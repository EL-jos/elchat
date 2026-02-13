<?php

namespace App\Services\ia;

use App\Models\Chunk;
use App\Models\KnowledgeQualityScore;
use App\Models\Site;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KnowledgeQualityService
{
    protected string $qdrantUrl;
    protected string $qdrantCollection;

    public function __construct()
    {
        $this->qdrantUrl = config('qdrant.url');
        $this->qdrantCollection = config('qdrant.collection', 'chunks');
    }

    /**
     * Calcul du score global de qualité de connaissance pour un site
     */
    public function calculateForSite(Site $site): KnowledgeQualityScore
    {
        $now = now();

        // --- 1️⃣ Couverture : % chunks non vides ---
        $totalChunks = Chunk::where('site_id', $site->id)->count();
        $nonEmptyChunks = Chunk::where('site_id', $site->id)
            ->whereRaw('LENGTH(TRIM(text)) > 0')
            ->count();

        $coverageScore = $totalChunks > 0
            ? min(100, ($nonEmptyChunks / $totalChunks) * 100)
            : 0;

        // --- 2️⃣ Qualité des données : longueur moyenne des chunks ---
        $avgLength = Chunk::where('site_id', $site->id)
            ->selectRaw('AVG(LENGTH(TRIM(text))) as avg_length')
            ->value('avg_length') ?? 0;

        $dataQualityScore = min(100, ($avgLength / 500) * 100);

        // --- 3️⃣ Fraîcheur : % de chunks créés ou mis à jour dans les 30 derniers jours ---
        $freshChunks = Chunk::where('site_id', $site->id)
            ->where(function($q) use ($now) {
                $q->where('created_at', '>=', $now->subDays(30))
                    ->orWhere('updated_at', '>=', $now->subDays(30));
            })->count();

        $freshnessScore = $totalChunks > 0
            ? min(100, ($freshChunks / $totalChunks) * 100)
            : 0;

        // --- 4️⃣ Richesse sémantique : score vectoriel depuis Qdrant ---
        $semanticScore = $this->calculateSemanticScoreWithQdrant($site);

        //Log::info($semanticScore);

        // --- 5️⃣ Score global pondéré ---
        $globalScore = ($coverageScore * 0.3)
            + ($dataQualityScore * 0.3)
            + ($semanticScore * 0.2)
            + ($freshnessScore * 0.2);

        // --- 6️⃣ Enregistrement ou mise à jour ---
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

    /**
     * Calcul du score sémantique basé sur la diversité des embeddings dans Qdrant
     */
    protected function calculateSemanticScoreWithQdrant(Site $site): float
    {
        try {
            $response = Http::timeout(10)->post(
                "{$this->qdrantUrl}/collections/{$this->qdrantCollection}/points/scroll",
                [
                    'filter' => [
                        'must' => [
                            ['key' => 'site_id', 'match' => ['value' => $site->id]]
                        ]
                    ],
                    'with_vector' => true,
                ]
            );

            $points = $response->json('result') ?? [];
            if (empty($points)) return 0;

            $vectors = collect($points)->pluck('vector')->filter(fn($v) => !empty($v));

            if ($vectors->isEmpty()) return 0;

            $dim = count($vectors->first());
            $meanVector = array_fill(0, $dim, 0);

            foreach ($vectors as $vec) {
                foreach ($vec as $i => $val) {
                    $meanVector[$i] += $val / count($vectors);
                }
            }

            $varianceSum = 0;
            foreach ($vectors as $vec) {
                foreach ($vec as $i => $val) {
                    $varianceSum += pow($val - $meanVector[$i], 2);
                }
            }

            $varianceScore = sqrt($varianceSum / (count($vectors) * $dim));

            // Normalisation simple sur 0-100
            return min(100, $varianceScore * 100);

        } catch (\Throwable $e) {
            Log::error("Erreur calcul score vectoriel site {$site->id}", [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
}
