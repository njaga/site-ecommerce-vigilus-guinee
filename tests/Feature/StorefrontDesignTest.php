<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\LaunchPreflight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontDesignTest extends TestCase
{
    use RefreshDatabase;

    private function product(bool $quoteOnly = false): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'cameras-test'],
            ['name' => 'Caméras de test', 'is_active' => true, 'sort_order' => 1],
        );

        return Product::create([
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'name' => 'Caméra Dahua fictive pour recette visuelle',
            'sku' => 'SKU-DESIGN-EXAMPLE-2026',
            'slug' => 'dahua-fictive-design',
            'short_description' => 'Une description de test, sans prétention commerciale.',
            'description' => 'Un descriptif fictif destiné uniquement aux tests de la nouvelle fiche.',
            'specifications' => ['Résolution fictive' => '4 MP', 'Réseau' => 'IP'],
            'price_gnf' => 275000,
            'price_on_request' => $quoteOnly,
            'stock_qty' => 5,
            'is_active' => true,
            'is_featured' => true,
        ]);
    }

    public function test_new_header_has_real_brand_asset_reference_and_mobile_navigation(): void
    {
        $this->get(route('home'))->assertOk()
            ->assertSee('Vigilus — logo du groupe')
            ->assertSee('class="mobile-menu md:hidden"', false)
            ->assertSee('aria-label="Menu mobile"', false)
            ->assertSee('Rechercher un produit Dahua')
            ->assertSee('Guide vidéosurveillance')
            ->assertSee('La bonne technologie pour')
            ->assertSee('Aucun paiement en ligne');
    }

    public function test_featured_product_uses_its_real_associated_photo_not_fake_product_data(): void
    {
        $product = $this->product();
        $product->images()->create([
            'path' => 'products/photo-reference-fictive.jpg',
            'alt' => 'Photo de démonstration du produit',
            'sort_order' => 0,
        ]);

        $this->get(route('home'))->assertOk()
            ->assertSee('storage/products/photo-reference-fictive.jpg')
            ->assertSee('Photo de démonstration du produit')
            ->assertSee('Références à découvrir');
    }

    public function test_catalog_has_collapsible_filters_and_canonical_noindex_behavior(): void
    {
        $this->get(route('catalog'))->assertOk()
            ->assertSee('class="catalog-filters"', false)
            ->assertSee('Affiner la sélection')
            ->assertSee('Aucune référence pour cette sélection');

        $this->get(route('catalog', ['q' => 'Modele non existant']))
            ->assertOk()->assertSee('noindex,follow');

        $product = $this->product();
        $this->get(route('catalog'))->assertOk()
            ->assertSee($product->sku)->assertSee('product-card');
    }

    public function test_gallery_has_keyboard_buttons_accessible_labels_and_main_photo(): void
    {
        $product = $this->product();
        $product->images()->create([
            'path' => 'products/dahua-photo-1.jpg',
            'alt' => 'Vue principale de la caméra fictive',
            'sort_order' => 0,
        ]);
        $product->images()->create([
            'path' => 'products/dahua-photo-2.jpg',
            'alt' => 'Vue arrière de la caméra fictive',
            'sort_order' => 1,
        ]);

        $this->get(route('product.show', $product->slug))->assertOk()
            ->assertSee('data-product-gallery', false)
            ->assertSee('id="gallery-main-image"', false)
            ->assertSee('data-gallery-thumb', false)
            ->assertSee('aria-pressed="true"', false)
            ->assertSee('aria-pressed="false"', false)
            ->assertSee('Vue arrière de la caméra fictive')
            ->assertSee('Caractéristiques')
            ->assertSee('Ajouter au panier');
    }

    public function test_quote_only_price_never_appears_as_offer_even_with_stale_price_value(): void
    {
        $product = $this->product(quoteOnly: true);
        $this->get(route('product.show', $product->slug))->assertOk()
            ->assertSee('Prix sur devis')
            ->assertDontSee('275 000')
            ->assertDontSee('Ajouter au panier')
            ->assertDontSee('https://schema.org/InStock');
        $this->get(route('catalog'))->assertOk()->assertDontSee('275 000');
    }

    public function test_cart_remove_action_is_visible_and_works_without_javascript(): void
    {
        $product = $this->product();
        $this->withSession(['cart' => [$product->id => 2]])
            ->get(route('cart.index'))->assertOk()
            ->assertSee('Retirer')
            ->assertSee('aria-label="Retirer', false);

        $this->withSession(['cart' => [$product->id => 2]])
            ->post(route('cart.update'), [
                'product_id' => $product->id, 'quantity' => 0,
            ])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('cart', []);
    }

    public function test_admin_sidebar_shows_clear_active_sections_only_to_authorized_users(): void
    {
        $this->get(route('admin.products.index'))->assertRedirect(route('login'));

        $admin = User::create([
            'name' => 'Design Reviewer',
            'email' => 'design@example.test',
            'password' => 'test-password-long-123',
            'is_admin' => true,
        ]);
        $this->actingAs($admin)->get(route('admin.products.index'))->assertOk()
            ->assertSee('admin-rail')
            ->assertSee('admin-rail-nav')
            ->assertSee('Produits Dahua')
            ->assertSee('Migration SEO')
            ->assertSee('Déconnexion')
            ->assertSee('noindex,nofollow');
    }

    public function test_launch_preflight_requires_local_approved_brand_asset(): void
    {
        config()->set('site.brand_logo_url', 'https://raw.githubusercontent.com/example/official.png');
        config()->set('site.hero_illustration_url', 'https://raw.githubusercontent.com/example/illustration.jpg');

        $checks = collect(app(LaunchPreflight::class)->checks());
        $this->assertFalse($checks->firstWhere('key', 'brand-logo')['passed']);
        $this->assertFalse($checks->firstWhere('key', 'hero-media')['passed']);
    }
}
