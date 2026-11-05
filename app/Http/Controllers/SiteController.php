<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SiteController
{
    public function robots(): Response
    {
        if (! config('seo.default_indexable')) {
            return response("User-agent: *\nDisallow: /\n", 200)
                ->header('Content-Type', 'text/plain; charset=UTF-8');
        }

        return response(implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /connexion',
            'Disallow: /deconnexion',
            'Disallow: /panier',
            'Disallow: /commande',
            'Disallow: /suivi-commande',
            'Disallow: /*?q=',
            'Disallow: /*?tri=',
            'Sitemap: '.route('sitemap'),
            '',
        ]))->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(): Response
    {
        if (! config('seo.default_indexable')) {
            return response(
                '<?xml version="1.0" encoding="UTF-8"?>'
                    .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>',
                200,
            )->header('Content-Type', 'application/xml; charset=UTF-8')
             ->header('X-Robots-Tag', 'noindex, nofollow');
        }

        $urls = [
            [route('home'), now()],
            [route('catalog'), now()],
            [route('solution.videosurveillance'), now()],
        ];

        foreach (Category::where('is_active', true)->whereHas('products', fn ($q) => $q->where('is_active', true))->get() as $category) {
            $urls[] = [route('category.show', $category), $category->updated_at];
        }

        foreach (Product::published()->get() as $product) {
            $urls[] = [route('product.show', $product->slug), $product->updated_at];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as [$url, $updatedAt]) {
            $xml .= '<url><loc>'.htmlspecialchars($url, ENT_XML1, 'UTF-8').'</loc>';
            if ($updatedAt) {
                $xml .= '<lastmod>'.$updatedAt->toDateString().'</lastmod>';
            }
            $xml .= '</url>'."\n";
        }
        $xml .= '</urlset>';

        return response($xml)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
