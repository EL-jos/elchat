<?php
// app/Services/CrawlService.php

namespace App\Services;

use App\Models\Site;
use App\Models\Page;
use App\Models\CrawlJob;
use Illuminate\Support\Str;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Log; // Nécessaire pour les logs

class CrawlService
{
    /**
     * Lance le crawl d’un site selon sa profondeur et respecte le crawl-delay.
     * Cette méthode encapsule toute la logique de crawling.
     *
     * @param Site $site
     * @return void
     */
    public function crawlSite(Site $site): void
    {
        Log::alert("🚀 Démarrage du crawl pour le site ID {$site->id} - URL: {$site->url}");

        // Initialisation des structures de données pour éviter les boucles/doublons de liens
        $visited = [];          // URLs déjà traitées (crawlées et Page potentiellement créée)
        $seenInQueue = [];      // URLs déjà présentes dans la file d'attente (évite les doublons dans la queue)
        $contentCache = [];     // Structure temporaire pour éviter les doublons de contenu (titre + contenu)

        $baseUrl = rtrim($site->url, '/') . '/'; // Assurez-vous que l'URL de base se termine par /
        $baseHost = parse_url($baseUrl, PHP_URL_HOST); // Extraire le host pour filtrer les liens internes

        // Initialisation de la file d'attente avec l'URL de base et une profondeur de 0
        $queue = [
            [
                'url' => $baseUrl,
                'depth' => 0,
            ]
        ];

        $site->update(['status' => 'crawling']);
        Log::info("Démarrage du crawl pour le site {$site->url} (profondeur: {$site->crawl_depth})");

        // Boucle principale de crawl
        while (!empty($queue)) {
            // Extraire le prochain élément de la file (FIFO - First In, First Out)
            $current = array_shift($queue);
            $url = $current['url'];
            $depth = $current['depth'];

            // --- VÉRIFICATION DE LA PROFONDEUR ---
            // Si la profondeur actuelle dépasse la limite autorisée pour ce site, ignorer cette URL
            if ($depth > $site->crawl_depth) {
                Log::debug("Profondeur max atteinte, URL ignorée: {$url}", ['max_depth' => $site->crawl_depth, 'current_depth' => $depth]);
                continue;
            }
            // --- FIN DE LA VÉRIFICATION ---

            // Normaliser l'URL avant de la traiter (et avant de vérifier visited/seenInQueue)
            $normalizedUrl = $this->normalizeUrl($url);

            // Vérifier si l'URL normalisée a déjà été visitée pour éviter de la traiter à nouveau
            if (in_array($normalizedUrl, $visited, true)) {
                Log::debug("URL déjà visitée (normalisée), ignorée: {$url} (depuis: {$normalizedUrl})");
                continue;
            }

            // Marquer l'URL normalisée comme visitée
            $visited[] = $normalizedUrl;

            // Créer un enregistrement de CrawlJob pour cette page spécifique
            $crawlJob = CrawlJob::create([
                'id' => (string) Str::uuid(),
                'site_id' => $site->id,
                'page_url' => $url, // Garder l'URL originale pour le CrawlJob
                'status' => 'processing',
            ]);

            try {
                // Appliquer un éventuel délai de crawl pour ne pas surcharger le serveur
                if ($site->crawl_delay > 0) {
                    usleep($site->crawl_delay * 1000000); // Convertir secondes en microsecondes
                }

                // Initialiser le client HttpBrowser pour simuler un navigateur (sans JS)
                $client = new HttpBrowser(HttpClient::create([
                    'timeout' => 60, // Temps d'attente max pour la réponse HTTP
                ]));

                // Effectuer la requête GET
                $client->request('GET', $url);

                // Obtenir le Crawler pour analyser le HTML récupéré
                $crawler = $client->getCrawler();

                // --- EXTRACTION DU CONTENU TEXTUEL ---
                $text = $crawler->filter('body')->text('', true); // Récupérer le texte du body, excluant les enfants
                $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'); // Décoder les entités HTML
                $text = preg_replace('/\s+/', ' ', trim($text)); // Remplacer les espaces multiples par un seul espace
                // Tronquer le texte pour la comparaison (optionnel, mais peut améliorer les performances)
                $textForComparison = mb_substr($text, 0, 200); // Premier 200 caractères (à ajuster)

                // --- EXTRACTION DU TITRE ---
                $title = '';
                $titleElement = $crawler->filter('title')->first(); // Sélectionner la balise <title>
                if ($titleElement->count() > 0) {
                    $title = trim(html_entity_decode($titleElement->text(), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                }
                // Normaliser le titre pour la comparaison (enlever espaces, accents éventuellement)
                $titleForComparison = $this->normalizeContentString($title);

                // Vérifier si ce couple (titre, contenu_tronqué) a déjà été rencontré (doublon de contenu)
                $contentSignature = md5($titleForComparison . '|' . $textForComparison);
                if (isset($contentCache[$contentSignature])) {
                    Log::info("Contenu dupliqué détecté, page ignorée: {$url} (Titre: '{$title}')");
                    $crawlJob->update(['status' => 'skipped_content_duplicate']); // Statut optionnel pour suivi
                    continue; // Passer à la suite sans créer de Page ni extraire de liens
                }

                // --- EXTRACTION DES LIENS INTERNES ---
                $links = [];
                // Filtrer toutes les balises <a> ayant un attribut href
                $crawler->filter('a[href]')->each(function (Crawler $node) use (&$links, $url, $baseHost) {
                    $href = trim($node->attr('href')); // Récupérer et nettoyer l'attribut href

                    // Ignorer les ancres, les liens mailto:, javascript:, etc.
                    if (
                        !$href ||
                        str_starts_with($href, '#') ||
                        preg_match('/^(mailto|tel|javascript|ftp|data):/i', $href)
                    ) {
                        return; // Passer au lien suivant
                    }

                    // Résoudre l'URL relative pour obtenir l'URL absolue
                    $absoluteLink = $this->resolveUrl($href, $url);

                    // Si la résolution a réussi
                    if ($absoluteLink) {
                        // Extraire le host de l'URL résolue
                        $linkHost = parse_url($absoluteLink, PHP_URL_HOST);

                        // Vérifier si le host correspond à celui du site initial (lien interne)
                        if ($linkHost === $baseHost) {
                            // Normaliser le lien (par exemple, supprimer le slash final, gérer les paramètres)
                            $cleanLink = $this->normalizeUrl($absoluteLink);

                            // Vérifier qu'il n'est pas déjà dans la liste des liens extraits pour cette page
                            // et qu'il n'est pas vide après normalisation
                            if ($cleanLink && !in_array($cleanLink, $links, true)) {
                                $links[] = $cleanLink;
                            }
                        }
                    }
                });

                // Logguer des infos sur la page crawlée (utile pour le débogage)
                Log::debug("Page crawlée : {$url}", [
                    'title' => $title,
                    'links_count' => count($links),
                    'depth' => $depth, // Inclure la profondeur dans les logs
                ]);

                // Ajouter la signature de contenu à notre cache temporaire
                $contentCache[$contentSignature] = true;

                // Créer un enregistrement Page dans la base de données
                Page::create([
                    'id' => (string) Str::uuid(),
                    'site_id' => $site->id,
                    'crawl_job_id' => $crawlJob->id,
                    'url' => $url, // Garder l'URL originale pour la Page
                    'title' => $title,
                    'content' => $text,
                ]);

                // Mettre à jour le statut du CrawlJob pour cette page
                $crawlJob->update(['status' => 'done']);

                // --- AJOUTER LES NOUVEAUX LIENS À LA FILE D'ATTENTE ---
                foreach ($links as $link) {
                    // $link est déjà normalisé ici grace à normalizeUrl dans la boucle each
                    $normalizedLink = $link; // Pour plus de clarté

                    // Vérifier qu'il n'est ni déjà visité (normalisé), ni déjà dans la file (normalisé)
                    if (!in_array($normalizedLink, $visited, true) && !in_array($normalizedLink, $seenInQueue, true)) {
                        $seenInQueue[] = $normalizedLink; // Marquer comme vu dans la file

                        // Ajouter le lien normalisé à la file avec la profondeur incrémentée
                        $queue[] = [
                            'url' => $normalizedLink,
                            'depth' => $depth + 1,
                        ];
                    }
                }

            } catch (\Throwable $e) {
                // Gérer les erreurs de crawl pour une URL spécifique
                Log::error("Erreur lors du crawl de {$url}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Mettre à jour le statut du CrawlJob pour cette page en cas d'erreur
                $crawlJob->update([
                    'status' => 'error',
                    'error_message' => $e->getMessage(),
                ]);
                // On continue le crawl des autres URLs dans la file, on ne propage pas l'exception ici
                // pour ne pas échouer le Job entièrement à cause d'une seule page inaccessible.
            }
        }

        $site->update(['status' => 'ready']);
        Log::info("Crawl terminé pour le site {$site->url}");
    }


    /**
     * Normalise une URL pour la comparaison.
     * Exemple de normalisation : suppression des slashs finaux, minuscules pour le host, tri des paramètres (optionnel).
     *
     * @param string $url L'URL à normaliser.
     * @return string L'URL normalisée.
     */
    private function normalizeUrl(string $url): string
    {
        // Parser l'URL
        $components = parse_url($url);
        if ($components === false) {
            // Si l'URL est invalide, la retourner telle quelle ou laisser tomber ?
            // Pour ce cas, on la traite comme une chaîne vide ou on la retourne.
            // On choisit de la retourner pour ne pas perturber la logique principale.
            return $url;
        }

        // Normaliser le scheme et le host en minuscules
        $scheme = strtolower($components['scheme'] ?? '');
        $host = strtolower($components['host'] ?? '');

        // Normaliser le path (enlever slash final, nettoyer)
        $path = $components['path'] ?? '/';
        $path = $this->normalizePath($path);

        // Reconstituer l'URL
        $normalized = $scheme . '://' . $host . $path;

        // Ajouter port si spécifié
        if (isset($components['port'])) {
            $normalized .= ':' . $components['port'];
        }

        // Ajouter query string si spécifiée (et la normaliser si pertinent, ici on la laisse telle quelle)
        if (isset($components['query'])) {
            $normalized .= '?' . $components['query']; // Pour une normalisation avancée, trier les paramètres ici
        }

        // Ajouter fragment si spécifié (généralement ignoré pour la comparaison de page, mais on le garde ici si présent)
        if (isset($components['fragment'])) {
            $normalized .= '#' . $components['fragment'];
        }

        return $normalized;
    }


    /**
     * Normalise un morceau de texte (titre par exemple) pour la comparaison de contenu.
     *
     * @param string $str
     * @return string
     */
    private function normalizeContentString(string $str): string
    {
        // Convertir en minuscules
        $str = mb_strtolower($str, 'UTF-8');
        // Enlever les espaces multiples et trim
        $str = preg_replace('/\s+/', ' ', trim($str));
        // Optionnellement, vous pouvez retirer la ponctuation ou les accents ici si pertinent
        // $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str); // Cela retire les accents
        return $str;
    }


    /**
     * Résout une URL relative à partir d'une URL de base.
     * Remplace la logique de UriHttp::resolve.
     *
     * @param string $relativeUrl L'URL relative à résoudre.
     * @param string $baseUrl L'URL de base.
     * @return string|null L'URL absolue résolue, ou null si elle ne peut pas l'être.
     */
    private function resolveUrl(string $relativeUrl, string $baseUrl): ?string
    {
        // Si l'URL est déjà absolue (contient un schéma comme http:// ou https://)
        if (parse_url($relativeUrl, PHP_URL_SCHEME)) {
            return $relativeUrl;
        }

        // Si c'est un chemin absolu (/path/to/resource)
        if (str_starts_with($relativeUrl, '/')) {
            $baseComponents = parse_url($baseUrl);
            if ($baseComponents === false) {
                return null; // URL de base invalide
            }
            $scheme = $baseComponents['scheme'] ?? 'http';
            $host = $baseComponents['host'] ?? '';
            $port = isset($baseComponents['port']) ? ':' . $baseComponents['port'] : '';
            return $scheme . '://' . $host . $port . $relativeUrl;
        }

        // Sinon, c'est un chemin relatif (./path, ../path, path/to/resource)
        // Calculer le chemin de base à partir de l'URL de base
        $basePath = dirname(parse_url($baseUrl, PHP_URL_PATH) ?: '/');
        if ($basePath === '.') {
            $basePath = '/'; // Si baseUrl est une racine, basePath est '/'
        }
        $newPath = $basePath . '/' . $relativeUrl;

        // Nettoyer le chemin pour résoudre ./ et ../
        $newPath = $this->normalizePath($newPath);

        // Reconstruire l'URL absolue complète
        $baseComponents = parse_url($baseUrl);
        if ($baseComponents === false) {
            return null; // URL de base invalide
        }
        $scheme = $baseComponents['scheme'] ?? 'http';
        $host = $baseComponents['host'] ?? '';
        $port = isset($baseComponents['port']) ? ':' . $baseComponents['port'] : '';
        return $scheme . '://' . $host . $port . $newPath;
    }

    /**
     * Normalise un chemin en résolvant ./ et ../
     *
     * @param string $path Le chemin à normaliser.
     * @return string Le chemin normalisé.
     */
    private function normalizePath(string $path): string
    {
        $parts = explode('/', $path);
        $normalized = [];

        foreach ($parts as $part) {
            if ($part === '.' || $part === '') {
                continue; // Ignore le répertoire courant et les segments vides
            }
            if ($part === '..') {
                // Remonter d'un niveau : retirer le dernier élément du chemin normalisé
                array_pop($normalized);
            } else {
                // Ajouter le segment au chemin normalisé
                $normalized[] = $part;
            }
        }

        // Recombiner les parties
        $result = implode('/', $normalized);

        // Préserver le slash initial si le chemin original en avait un
        if (str_starts_with($path, '/')) {
            $result = '/' . $result;
        }

        // Préserver le slash final si le chemin original en avait un
        if (substr($path, -1) === '/' && substr($result, -1) !== '/') {
            $result .= '/';
        }

        return $result;
    }
}
