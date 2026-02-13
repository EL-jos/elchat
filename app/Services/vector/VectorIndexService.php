<?php

namespace App\Services\vector;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VectorIndexService
{
    protected string $endpoint;
    protected string $collection;

    public function __construct()
    {
        //dd(config('qdrant'));
        $this->endpoint  = config('qdrant.url'); // http://127.0.0.1:6333
        $this->collection = 'chunks';
    }

    /**
     * Upsert d’un chunk dans Qdrant
     */
    public function upsertChunk(
        string $chunkId,
        array $embedding,
        array $payload
    ): void {
        try {
            Http::timeout(5)->put(
                "{$this->endpoint}/collections/{$this->collection}/points",
                [
                    'points' => [
                        [
                            'id'      => $chunkId,   // UUID MySQL
                            'vector'  => $embedding,
                            'payload' => $payload,
                        ],
                    ],
                ]
            );
        } catch (\Throwable $e) {
            // ⚠️ On LOG, mais on ne casse JAMAIS l’indexation
            Log::error('Qdrant upsert failed', [
                'chunk_id' => $chunkId,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
