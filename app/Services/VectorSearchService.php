<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VectorSearchService
{
    protected string $baseUrl;
    protected string $collection;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl   = config('qdrant.url');
        $this->collection = config('qdrant.collection');
        $this->timeout    = config('qdrant.timeout', 8);
    }

    /**
     * Recherche vectorielle principale
     *
     * @return array [
     *   [
     *     'id' => 'uuid',
     *     'score' => float,
     *     'payload' => [...]
     *   ]
     * ]
     */
    public function search(
        array $embedding,
        string $siteId,
        int $limit = 12,
        float $scoreThreshold = 0.25
    ): array {
        try {
            $response = Http::timeout($this->timeout)->post(
                "{$this->baseUrl}/collections/{$this->collection}/points/search",
                [
                    'vector' => $embedding,
                    'limit'  => $limit,
                    'with_payload' => true,
                    'score_threshold' => $scoreThreshold,

                ]
            );

            //dd($response->json(), $siteId, $embedding);
            if ($response->failed()) {
                Log::error('Qdrant search failed', [
                    'site_id' => $siteId,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);
                return [];
            }


            return $response->json('result') ?? [];

        } catch (\Throwable $e) {
            Log::error('Qdrant search exception', [
                'site_id' => $siteId,
                'error'   => $e->getMessage(),
            ]);

            return [];
        }
    }
}
