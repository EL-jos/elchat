<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    /**
     * Retourne un embedding pour un texte
     * MVP dev : OpenRouter (gratuit)
     * Production : OpenAI
     */
    public function getEmbedding(string $text): array
    {
        // Exemple MVP: placeholder vecteur 1536 dimensions
        // Remplacer par appel API OpenRouter/OpenAI en prod
        /*$embedding = array_fill(0, 1536, 0.01); // valeur dummy pour test
        return $embedding;*/


        // Exemple OpenRouter API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY')
        ])->post('https://openrouter.ai/api/v1/embeddings', [
            'model' => 'text-embedding-3-small',
            'input' => $text
        ]);
        return $response->json()['data'][0]['embedding'];

    }
}
