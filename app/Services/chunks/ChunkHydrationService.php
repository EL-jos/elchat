<?php

namespace App\Services\chunks;

use App\Models\Chunk;

class ChunkHydrationService
{
    /**
     * Hydrate les résultats Qdrant avec MySQL
     */
    public function hydrate(array $qdrantResults): array
    {
        if (empty($qdrantResults)) {
            return [];
        }

        // 1️⃣ Extraire les IDs Qdrant
        $ids = collect($qdrantResults)
            ->pluck('id')
            ->filter()
            ->values()
            ->toArray();

        if (empty($ids)) {
            return [];
        }

        // 2️⃣ Charger les chunks MySQL
        $chunks = Chunk::whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        // 3️⃣ Fusion Qdrant + MySQL
        $hydrated = [];

        foreach ($qdrantResults as $result) {
            $chunk = $chunks->get($result['id']);

            if (!$chunk) {
                continue; // sécurité prod
            }

            $hydrated[] = [
                'id'           => $chunk->id,
                'text'         => $chunk->text,
                'vector_score' => $result['score'] ?? 0.0,
                'priority'     => $chunk->priority ?? 100,
                'source_type'  => $chunk->source_type ?? 'unknown',
                'metadata'     => $chunk->metadata,
            ];
        }

        return $hydrated;
    }
}
