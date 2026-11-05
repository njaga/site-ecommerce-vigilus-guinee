<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Support\CategoryVisuals;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveSearchAndBrandTest extends TestCase
{
    use RefreshDatabase;

    private function category(string $slug = 'cameras-ip', bool $enabled = true): Category
    {
        return Category::create([
            'name' => $slug === 'cameras-ip' ? 'Caméras IP' : 'Enregistreurs NVR',
            'slug' => $slug,
            'is_active' => $enabled,
        ]);
    }

    private function product(
        Category $category,
        string $sku = 'DAH-EXAMPLE-001',
        bool $visible = true,
        bool $quoteOnly = false
    ): Product {
        return Product::create([
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'sku' => $sku,
            'slug' => strtolower($sku),
            'name' => 'Caméra Dahua de test '.$sku,
            'short_description' => 'Équipement destiné exclusivement aux tests.',
            'price_gnf' => 329000,
            'price_on_request' => $quoteOnly,
            'stock_qty' => 4,
            'is_active' => $visible,
            'is_featured' => $visible,
        ]);
    }

    public function test_live_search_requires_two_characters_and_never_caches_private_results(): void
    {
        $this->getJson(route('search.suggestions'))->assertOk()
            ->assertJsonPath('products', [])
            ->assertJsonPath('categories', []);
        $this->getJson(route('search.suggestions', ['q' => 'D']))->assertOk()
            ->assertJsonPath('products', []);
        $this->getJson(route('search.suggestions', ['q' => 'Dahua']))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $this->getJson(route('search.suggestions', ['q' => str_repeat('x', 81)]))
            ->assertUnprocessable()->assertJsonValidationErrors('q');
    }

    public function test_only_active_product_in_active_category_is_suggested(): void
    {
        $enabled = $this->category();
        $visible = $this->product($enabled, 'DAH-PUBLIC-001');
        $this->product($enabled, 'DAH-HIDDEN-002', false);
        $this->product($this->category('nvr-disabled', false), 'DAH-DISABLED-003');

        $response = $this->getJson(route('search.suggestions', ['q' => 'DAH']))
            ->assertOk()->assertJsonCount(1, 'products')
            ->assertJsonPath('products.0.sku', $visible->sku)
            ->assertJsonPath('products.0.url', route('product.show', $visible->slug))
            ->assertJsonMissing(['sku' => 'DAH-HIDDEN-002'])
            ->assertJsonMissing(['sku' => 'DAH-DISABLED-003']);

        $this->assertArrayNotHasKey('procurement_price', $response->json('products.0'));
        $this->assertArrayNotHasKey('supplier_name', $response->json('products.0'));
        $this->assertArrayNotHasKey('supplier_discount', $response->json('products.0'));
    }

    public function test_suggestions_limit_results_to_six_products(): void
    {
        $category = $this->category();
        for ($i = 1; $i <= 11; $i++) {
            $this->product($category, sprintf('DAH-MODEL-%03d', $i));
        }

        $this->getJson(route('search.suggestions', ['q' => 'DAH']))
            ->assertOk()->assertJsonCount(6, 'products');
    }

    public function test_explicit_quote_only_never_returns_a_numeric_price(): void
    {
        $product = $this->product($this->category(), 'DAH-QUOTE-001', true, true);

        $this->getJson(route('search.suggestions', ['q' => 'DAH-QUOTE']))
            ->assertOk()
            ->assertJsonPath('products.0.sku', $product->sku)
            ->assertJsonPath('products.0.price_on_request', true)
            ->assertJsonPath('products.0.price_gnf', null);
    }

    public function test_relevant_categories_are_suggested_only_when_nonempty_and_active(): void
    {
        $enabled = $this->category();
        $this->product($enabled);
        $inactive = $this->category('nvr-disabled', false);
        $this->product($inactive, 'NVR-INACTIVE-001');

        $this->getJson(route('search.suggestions', ['q' => 'Caméras']))
            ->assertOk()
            ->assertJsonPath('categories.0.name', 'Caméras IP')
            ->assertJsonPath('categories.0.url', route('category.show', $enabled));

        $this->getJson(route('search.suggestions', ['q' => 'Enregistreurs']))
            ->assertOk()->assertJsonPath('categories', []);
    }

    public function test_exact_sku_results_take_precedence_over_name_only_matches(): void
    {
        $category = $this->category();
        $exact = $this->product($category, 'SKU-EXACT-2026');
        $match = $this->product($category, 'SKU-SECOND-2026');
        $match->update(['name' => 'Nom SKU-EXACT-2026 descriptif']);

        $this->getJson(route('search.suggestions', ['q' => 'SKU-EXACT-2026']))
            ->assertOk()->assertJsonPath('products.0.sku', $exact->sku);
    }

    public function test_home_modal_is_accessible_with_live_suggestions_and_group_logo(): void
    {
        $this->get(route('home'))->assertOk()
            ->assertSee('id="store-search-dialog"', false)
            ->assertSee('data-search-dialog', false)
            ->assertSee('aria-labelledby="store-search-title"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('data-suggestions-url=', false)
            ->assertSee('Recherches fréquentes')
            ->assertSee('Vigilus Groupe SA — logo officiel')
            ->assertSee('www.groupevigilus.com/media/site/logo.png')
            ->assertSee('À propos')
            ->assertSee('Pourquoi nous choisir')
            ->assertSee('Nos clients')
            ->assertSee('Nos partenaires');
    }

    public function test_home_categories_have_contextual_editorial_images(): void
    {
        $category = $this->category();
        $this->product($category);

        $this->get(route('home'))->assertOk()
            ->assertSee('category-image-card-img', false)
            ->assertSee('Illustration : Caméras de surveillance')
            ->assertSee('www.groupevigilus.com/media/services/videosurveillance.jpg');
    }

    public function test_contextual_images_for_equipment_families_are_distinct(): void
    {
        $camera = app(CategoryVisuals::class)->for(
            new Category(['name' => 'Caméras IP', 'slug' => 'cameras-ip'])
        );
        $access = app(CategoryVisuals::class)->for(
            new Category(['name' => 'Contrôle d’accès', 'slug' => 'controle-acces'])
        );
        $network = app(CategoryVisuals::class)->for(
            new Category(['name' => 'Switch réseau PoE', 'slug' => 'switch-reseau'])
        );
        $this->assertNotSame($camera['url'], $access['url']);
        $this->assertNotSame($camera['url'], $network['url']);
        $this->assertStringStartsWith('https://', $camera['url']);
    }

    public function test_about_is_public_and_preserves_group_client_scope(): void
    {
        $this->get(route('about'))->assertOk()
            ->assertSee('Pourquoi nous choisir')
            ->assertSee('Nos clients.')
            ->assertSee('Quelques références du Groupe Vigilus')
            ->assertSee('Nos partenaires et notre écosystème')
            ->assertSee('Marque du catalogue')
            ->assertSee('Vigilus Groupe SA')
            ->assertSee('La bonne référence');
    }

    public function test_about_is_in_sitemap_only_when_indexing_enabled(): void
    {
        config()->set('seo.default_indexable', true);
        $this->get(route('sitemap'))->assertOk()
            ->assertSee('/a-propos');
        config()->set('seo.default_indexable', false);
        $this->get(route('sitemap'))->assertOk()
            ->assertDontSee('/a-propos');
    }

    public function test_search_endpoint_rejects_post_and_has_no_legacy_payment_flow(): void
    {
        $this->post(route('search.suggestions'), ['q' => 'camera'])
            ->assertStatus(405);
        $this->assertFalse(config('commerce.online_payments_enabled'));
    }
}
