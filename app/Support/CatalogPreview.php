<?php

namespace App\Support;

class CatalogPreview
{
    /**
     * Temporary display mode: never allow drafts or 0-GNF mock prices to
     * escape a local non-indexed environment, even if the flag is copied.
     */
    public static function enabled(): bool
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        return (bool) config('catalog.preview_enabled', false)
            && app()->environment(['local', 'testing'])
            && in_array($host, ['localhost', '127.0.0.1', '::1'], true)
            && ! config('seo.default_indexable');
    }

    public static function displayPriceGnf(?int $actualPrice, bool $priceOnRequest): ?int
    {
        if (self::enabled()) {
            return 0;
        }

        return $priceOnRequest ? null : $actualPrice;
    }
}
