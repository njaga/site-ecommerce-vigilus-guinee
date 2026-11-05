<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\SeoRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SeoRedirector
{
    public function allowedHosts(): array
    {
        $main = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        return array_values(array_unique(array_filter([
            $main,
            ...config('seo.approved_legacy_hosts', []),
        ])));
    }

    public function validateHost(string $host): string
    {
        $host = strtolower(trim($host));
        if (! preg_match('/^[a-z0-9][a-z0-9.-]{0,253}$/', $host)
            || ! in_array($host, $this->allowedHosts(), true)) {
            throw ValidationException::withMessages([
                'source_host' => 'Domaine non autorisé. Ajoutez-le explicitement à SEO_APPROVED_LEGACY_HOSTS après vérification.',
            ]);
        }
        return $host;
    }

    public function normalizePath(string $value, string $field = 'source_path'): string
    {
        $value = trim($value);
        if ($value === '' || ! str_starts_with($value, '/') || str_starts_with($value, '//')
            || str_contains($value, '\\') || str_contains($value, '?')
            || str_contains($value, '#') || str_contains($value, '%')
            || preg_match('/[\x00-\x20\x7F]/', $value)
            || preg_match('#(?:^|/)\.{1,2}(?:/|$)#', $value)
            || mb_strlen($value) > 512) {
            throw ValidationException::withMessages([
                $field => 'Utilisez uniquement un chemin interne absolu, sans domaine, paramètres, encodage ou segments dangereux.',
            ]);
        }

        return $value === '/' ? '/' : rtrim($value, '/');
    }

    public function isProtectedSource(string $path): bool
    {
        if (in_array($path, ['/', '/produits', '/panier', '/commande',
            '/demande-de-devis', '/connexion', '/deconnexion',
            '/sitemap.xml', '/robots.txt', '/up',
            '/equipements-videosurveillance-conakry'], true)) {
            return true;
        }

        foreach (['/admin', '/commande/', '/panier/', '/suivi-commande', '/categories/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        // Un SKU actuel ne doit jamais être détourné vers une autre URL.
        if (str_starts_with($path, '/produits/')) {
            $slug = mb_substr($path, mb_strlen('/produits/'));
            return Product::where('slug', $slug)->exists();
        }

        return false;
    }

    public function isPublicDestination(string $path): bool
    {
        if (in_array($path, ['/', '/produits', '/demande-de-devis',
            '/equipements-videosurveillance-conakry'], true)) {
            return true;
        }

        if (preg_match('#^/categories/([a-zA-Z0-9_-]+)$#D', $path, $matches)) {
            return Category::where('slug', $matches[1])
                ->where('is_active', true)
                ->whereHas('products', fn ($q) => $q->where('is_active', true))
                ->exists();
        }

        if (preg_match('#^/produits/([a-zA-Z0-9_-]+)$#D', $path, $matches)) {
            return Product::published()->where('slug', $matches[1])->exists();
        }

        return false;
    }

    /**
     * Called only after Laravel raises a 404; can therefore handle old
     * /produits/{slug} URLs even though a new dynamic product route exists.
     */
    public function forNotFound(Request $request): ?RedirectResponse
    {
        if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return null;
        }

        $host = strtolower($request->getHost());
        if (! in_array($host, $this->allowedHosts(), true)) {
            return null;
        }

        try {
            $path = $this->normalizePath($request->getPathInfo());
        } catch (ValidationException) {
            return null;
        }

        $redirect = SeoRedirect::query()->where('source_host', $host)
            ->where('source_path', $path)->where('is_active', true)->first();

        if (! $redirect || ! $this->isPublicDestination($redirect->destination_path)) {
            return null;
        }

        // Fixe vers le domaine canonique configuré, jamais vers un URL user.
        $base = rtrim((string) config('app.url'), '/');
        return redirect()->away($base.$redirect->destination_path, 301)
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
