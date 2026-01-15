<?php

namespace App\Services;

class SimilarityService
{
    public function cosine(array $a, array $b): float
    {
        $dot = $normA = $normB = 0.0;

        foreach ($a as $i => $value) {
            $dot += $value * ($b[$i] ?? 0);
            $normA += $value ** 2;
            $normB += ($b[$i] ?? 0) ** 2;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
