<?php
namespace App\Services;

use App\Models\Site;
use App\Models\Chunk;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\UnansweredQuestion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatService
{

    public function __construct(
        protected EmbeddingService $embeddingService,
        protected SimilarityService $similarityService
    )
    {}

    /**
     * Traite la question et retourne la réponse factuelle
     */
    public function ask(string $question, Site $site, ?int $topK = 5, float $similarityThreshold = 0.45)
    {
        // 1. Générer embedding de la question
        $queryEmbedding = $this->embeddingService->getEmbedding($question);

        // 2. Récupérer les chunks du site
        $chunks = Chunk::whereHas('page', fn($q) => $q->where('site_id', $site->id))->get();

        // 3. Calculer similarité cosine (PHP)
        $chunksWithScore = $chunks->map(fn($chunk) => [
            'chunk' => $chunk,
            'score' => $this->similarityService->cosine(
                $queryEmbedding,
                $chunk->embedding
            )
        ]);

        // 4. Filtrer par score minimum
        $filtered = $chunksWithScore->filter(fn($c) => $c['score'] >= $similarityThreshold);

        // 5. Top K
        $topChunks = $filtered->sortByDesc('score')->take($topK)->pluck('chunk')->toArray();

        // 6. Construire prompt strict pour LLM
        $context = implode("\n", array_map(fn($c) => $c->text, $topChunks));

        // 7. Appel LLM (ici placeholder pour MVP)
        $answer = $this->callLLM($question, $context);

        return $answer;
    }

    /**
     * Réponse commerciale incarnée (mode production)
     */
    public function answer(Site $site, string $question, Conversation $conversation): string
    {

        $history = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'desc')
            ->skip(1)
            ->take(6)
            ->get()
            ->reverse()
            ->map(function ($m) {
                if ($m->role === 'bot') {
                    return [
                        'role' => 'assistant',
                        'content' => '[Réponse précédente donnée au client]',
                    ];
                }

                return [
                    'role' => 'user',
                    'content' => $m->content,
                ];
            })
            ->toArray();

        // 1️⃣ Embedding de la question
        $questionEmbedding = $this->embeddingService->getEmbedding($question);

        // 2️⃣ Charger les chunks du site
        $chunks = Chunk::whereHas('page', fn ($q) =>
        $q->where('site_id', $site->id)
        )->get();

        $scored = [];

        foreach ($chunks as $chunk) {
            $score = $this->similarityService->cosine(
                $questionEmbedding,
                $chunk->embedding
            );

            if ($score >= 0.30) {
                $scored[] = [
                    'text' => $chunk->text,
                    'score' => $score,
                ];
            }
        }

        // 3️⃣ Construire le contexte
        if (empty($scored)) {
            UnansweredQuestion::create([
                'site_id' => $site->id,
                'question' => $question,
            ]);

            // ⚠️ Fallback HUMAIN (clé de l’illusion)
            $context = "Nous n'avons pas communiqué publiquement cette information pour le moment.";
        } else {
            usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

            $context = collect(array_slice($scored, 0, 3))
                ->pluck('text')
                ->implode("\n\n---\n\n");
        }

        $isSelectionQuestion = preg_match('/moins cher|meilleur|choisir|recommander|quel/i', $question);
        if ($isSelectionQuestion && empty($scored)) {
            $context = "Nous proposons plusieurs produits, mais nous ne communiquons pas de classement par prix.";
        }


        return $this->callLLM($site, $question, $context, $history);
    }

    /**
     * Appel LLM avec PERSONA EMPLOYÉ INTERNE
     */
    private function callLLM(Site $site, string $question, string $context, array $history): string
    {
        $companyName = $site->name ?? parse_url($site->url, PHP_URL_HOST);

        $isBuyingIntent = preg_match('/prix|acheter|commander|disponible|livraison/i', $question);
        $isComparison = preg_match('/compar|différence|vs|meilleur/i', $question);
        $isConcern = preg_match('/durée|garantie|retour|risque|qualité/i', $question);


        $systemPrompt = <<<PROMPT
        Tu es un employé réel de l'entreprise "{$companyName}".

        RÈGLES STRICTES :
        - Tu parles à la PREMIÈRE PERSONNE (nous / chez nous / notre équipe).
        - Tu ne mentionnes JAMAIS :
          - le mot "contexte"
          - le site web
          - une source
          - une analyse
          - une intelligence artificielle
        - Tu réponds comme si tu travaillais réellement dans l'entreprise.
        - Ton ton est professionnel, naturel, commercial et rassurant.
        - Tu n'inventes JAMAIS une caractéristique technique, un prix ou une garantie.
        - Tu peux reformuler, expliquer ou valoriser, mais jamais créer une information factuelle.
        - Si intention d’achat : rassure et incite à passer à l’action
        - Si hésitation : rassure
        - Si comparaison : valorise sans dénigrer
        - Termine si possible par une proposition d’aide naturelle (sans forcer la vente).
        - Tu ne fais jamais de promesse engageante (résultat garanti, effet certain, engagement contractuel).
        - Si la conversation est déjà entamée, tu ne recommences jamais par une formule de salutation.

        RÈGLE ABSOLUE SUR LA CONVERSATION :
        - Les messages précédents servent UNIQUEMENT à comprendre le besoin du client.
        - Les informations factuelles doivent PROVENIR EXCLUSIVEMENT des "Informations internes".
        - Si une information n’est PAS présente dans les informations internes, tu dois :
          - rester général
          - ou proposer d’aider autrement
        - Tu ne dois JAMAIS déduire un produit, une offre ou un prix à partir d’une réponse précédente.
        INTERDICTION ABSOLUE :
        - Tu ne dois JAMAIS citer un nom de produit, pack ou offre
          s’il n’apparaît PAS explicitement mot pour mot
          dans les Informations internes.

        RÔLE :
        Conseiller commercial / employé de l’entreprise.
        PROMPT;

        $userPrompt = <<<PROMPT
        Informations internes à utiliser si pertinentes :
        {$context}

        Question d’un client :
        {$question}

        Réponds directement au client, comme si tu lui parlais en face.

        Type de demande :
        - Si la question concerne un PRODUIT → mets en avant ses bénéfices.
        - Si elle concerne un SERVICE → explique l’accompagnement.
        - Si elle est GÉNÉRALE → rassure et oriente.

        Signal détecté :
        - Intention d’achat : {$isBuyingIntent}
        - Comparaison : {$isComparison}
        - Inquiétude : {$isConcern}
        PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // 🧠 mémoire conversationnelle
        foreach ($history as $msg) {
            $messages[] = $msg;
        }

        // question actuelle (avec contexte RAG)
        $messages[] = [
            'role' => 'user',
            'content' => $userPrompt,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'meta-llama/llama-3.1-8b-instruct',
            'messages' => $messages,
            'temperature' => 0.6,
            'max_tokens' => 350,
        ]);

        return $response->json()['choices'][0]['message']['content']
            ?? "N'hésitez pas à nous contacter, nous serons ravis de vous aider.";
    }
}
