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
use Illuminate\Support\Facades\Log;

class CrawlService
{
    public function prepareQueue(Site $site): array
    {
        $queue = [];
        $visited = [];

        $baseUrl = rtrim($site->url, '/') . '/';
        $baseHost = parse_url($baseUrl, PHP_URL_HOST);

        Log::info("PrepareQueue start: baseUrl={$baseUrl}");

        // Si include_pages fourni → crawl ciblé
        if (!empty($site->include_pages)) {
            foreach ($site->include_pages as $path) {
                $resolved = $this->resolveUrl($path, $baseUrl);
                Log::info("Include page resolved: {$resolved}");
                $queue[] = ['url' => $resolved, 'depth' => 0];
            }
        } else {
            $queue[] = ['url' => $baseUrl, 'depth' => 0];
        }

        $allUrls = [];

        while (!empty($queue)) {
            $current = array_shift($queue);
            $url = $current['url'];
            $depth = $current['depth'];

            Log::info("Queue processing: url={$url}, depth={$depth}");

            if ($depth > $site->crawl_depth) continue;

            $normalizedUrl = $this->normalizeUrl($url);

            $skip = false;
            foreach ($site->exclude_pages ?? [] as $pattern) {
                if (str_contains($normalizedUrl, $pattern)) {
                    $skip = true; break;
                }
            }
            if ($skip || in_array($normalizedUrl, $visited)) continue;

            $visited[] = $normalizedUrl;
            $allUrls[] = ['url' => $normalizedUrl, 'depth' => $depth];

            // Extraction mock
            $links = $this->extractInternalLinksMock($normalizedUrl, $baseHost, $site);

            Log::info("Links found for {$normalizedUrl}: " . implode(', ', $links));

            foreach ($links as $link) {
                if (!in_array($link, $visited, true)) {
                    $queue[] = ['url' => $link, 'depth' => $depth + 1];
                }
            }
        }

        Log::info("PrepareQueue end: total urls=" . count($allUrls));

        return $allUrls;
    }
    public function crawlSinglePage(Site $site, string $url, int $depth, ?string $crawlJobId = null): ?Page
    {
        try {
            Log::info("Crawl start: {$url}");
            $client = new HttpBrowser(HttpClient::create(['timeout' => 60]));
            $client->request('GET', $url);
            $crawler = $client->getCrawler();

            $text = $crawler->filter('body')->text('', true);
            Log::info("Contenu body pour {$url}: " . substr($text, 0, 100)); // les 100 premiers caractères
            $text = preg_replace('/\s+/', ' ', trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            $title = $crawler->filter('title')->count() ? trim(html_entity_decode($crawler->filter('title')->text(), ENT_QUOTES | ENT_HTML5, 'UTF-8')) : '';

            return Page::create([
                'id' => (string) Str::uuid(),
                'site_id' => $site->id,
                'crawl_job_id' => $crawlJobId, // <-- ajouter
                'url' => $url,
                'title' => $title,
                'content' => $text,
            ]);
        } catch (\Throwable $e) {
            Log::error("Erreur crawl page: {$url}", ['error' => $e->getMessage()]);
            return null;
        }
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
        $normalized = $scheme . '://' . $host;

        if (isset($components['port'])) {
            $normalized .= ':' . $components['port'];
        }

        $normalized .= $path;

        // Ajouter query string si spécifiée (et la normaliser si pertinent, ici on la laisse telle quelle)
        if (isset($components['query'])) {
            $normalized .= '?' . $components['query']; // Pour une normalisation avancée, trier les paramètres ici
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
    /**
     * Simule l'extraction des liens internes d'une page pour préparer la queue de crawl.
     * Cette fonction ne fait pas le vrai crawl mais retourne les URLs internes filtrées.
     *
     * @param string $url L'URL de la page en cours de traitement
     * @param string $baseHost Le host du site (ex: example.com)
     * @param Site $site L'instance du site
     * @return array Tableau de liens internes à crawler
     */
    private function extractInternalLinksMock(string $url, string $baseHost, Site $site): array
    {
        $links = [];

        try {
            // Initialiser le client HttpBrowser
            $client = new HttpBrowser(HttpClient::create(['timeout' => 30]));
            $client->request('GET', $url);
            $crawler = $client->getCrawler();

            // Extraire tous les liens <a href="">
            $crawler->filter('a[href]')->each(function ($node) use (&$links, $baseHost, $site) {
                $href = trim($node->attr('href'));

                // Ignorer les liens inutiles
                if (!$href || str_starts_with($href, '#') || preg_match('/^(mailto|tel|javascript|ftp|data):/i', $href)) {
                    return;
                }

                // Résoudre l'URL relative
                $absoluteLink = $this->resolveUrl($href, $site->url);
                if (!$absoluteLink) return;

                $linkHost = parse_url($absoluteLink, PHP_URL_HOST);

                // Vérifier que le lien est interne au site
                if ($linkHost !== $baseHost) return;

                // Normaliser le lien
                $normalizedLink = $this->normalizeUrl($absoluteLink);

                // Vérifier les règles include_pages si définies
                if (!empty($site->include_pages)) {
                    $allowed = false;
                    foreach ($site->include_pages as $allowedPath) {
                        $parsedPath = parse_url($normalizedLink, PHP_URL_PATH) ?? '';
                        if (str_starts_with($parsedPath . '/', $allowedPath . '/')) {
                            $allowed = true;
                            break;
                        }
                    }
                    if (!$allowed) return;
                }

                // Vérifier les règles exclude_pages
                foreach ($site->exclude_pages ?? [] as $pattern) {
                    if (str_contains($normalizedLink, $pattern)) return;
                }

                // Ajouter le lien si pas déjà présent
                if (!in_array($normalizedLink, $links, true)) {
                    $links[] = $normalizedLink;
                }
            });
        } catch (\Throwable $e) {
            Log::warning("Impossible d'extraire les liens internes pour {$url}", [
                'site_id' => $site->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $links;
    }

}
