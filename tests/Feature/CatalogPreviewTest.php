<?php

namespace Tests\Feature;

use App\Models\CatalogImportBatch;
use App\Models\CatalogImportRow;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CatalogPreviewPreparer;
use App\Support\CatalogPreview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.url', 'http://localhost:8000');
        config()->set('catalog.preview_enabled', true);
        config()->set('seo.default_indexable', false);
    }

    private function category(bool $active = false): Category
    {
        return Category::create([
            'name' => 'Caméras IP',
            'slug' => 'cameras-preview',
            'is_active' => $active,
            'sort_order' => 1,
        ]);
    }

    private function draft(Category $category, ?int $realPrice = null): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'sku' => 'DAHUA-TEST-PREVIEW-001',
            'slug' => 'dahua-preview-001',
            'name' => 'Caméra IP Dahua de test sans prix validé',
            'short_description' => 'Modèle importé sans description commerciale définitive.',
            'description' => 'Informations techniques provisoires, à relire.',
            'specifications' => [],
            'price_gnf' => $realPrice,
            'price_on_request' => $realPrice === null,
            'stock_qty' => 0,
            'is_active' => false,
        ]);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Administration Aperçu',
            'email' => 'preview-admin@example.test',
            'password' => 'safe-preview-password-123',
            'is_admin' => true,
        ]);
    }

    public function test_local_preview_shows_all_drafts_in_catalog_and_0_gnf_with_inactive_category(): void
    {
        $category = $this->category();
        $product = $this->draft($category);

        $this->assertTrue(CatalogPreview::enabled());
        $this->get(route('home'))->assertOk()
            ->assertSee($category->name)
            ->assertSee($product->sku)
            ->assertSee('class="product-card-price">0 <span', false)
            ->assertSee('Illustration');

        $this->get(route('catalog'))->assertOk()
            ->assertSee($product->sku)
            ->assertSee('class="product-card-price">0 <span', false);
        $this->get(route('category.show', $category))->assertOk()
            ->assertSee($product->name);
        $this->get(route('product.show', $product->slug))->assertOk()
            ->assertSee('class="product-purchase-price">0 <span', false)
            ->assertSee('Illustration de la catégorie')
            ->assertSee('Ajouter au panier')
            ->assertDontSee('https://schema.org/InStock');

        $this->assertFalse($product->fresh()->is_active);
        $this->assertNull($product->fresh()->price_gnf);
        $this->assertTrue($product->fresh()->price_on_request);
    }

    public function test_existing_real_prices_are_never_overwritten_in_preview(): void
    {
        $product = $this->draft($this->category(true), 485000);
        $product->update(['price_on_request' => false]);

        $this->get(route('catalog'))->assertOk()
            ->assertSee('class="product-card-price">0 <span', false)
            ->assertDontSee('485 000');
        $this->get(route('product.show', $product->slug))->assertOk()
            ->assertSee('class="product-purchase-price">0 <span', false)
            ->assertDontSee('485 000');
        $this->getJson(route('search.suggestions', ['q' => 'DAHUA-TEST']))
            ->assertOk()
            ->assertJsonPath('products.0.price_gnf', 0)
            ->assertJsonPath('products.0.price_on_request', false);

        $this->assertSame(485000, $product->fresh()->price_gnf);
    }

    public function test_preview_cart_can_be_tested_without_stock_but_checkout_cannot_submit_zero_order(): void
    {
        $product = $this->draft($this->category());
        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertRedirect(route('cart.index'));

        $this->get(route('cart.index'))->assertOk()
            ->assertSee($product->name)
            ->assertSee('0 GNF');
        $this->get(route('checkout.create'))->assertOk()
            ->assertSee('0 GNF')
            ->assertSee('Envoi désactivé en aperçu');

        $this->post(route('checkout.store'), [])->assertForbidden();
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_public_host_disables_preview_even_if_flag_is_forced_on(): void
    {
        $product = $this->draft($this->category());

        config()->set('app.url', 'https://vigilus-guinee.example');
        $this->assertFalse(CatalogPreview::enabled());
        $this->get(route('catalog'))->assertDontSee($product->sku);
        $this->get(route('product.show', $product->slug))->assertNotFound();

        config()->set('app.url', 'http://localhost:8000');
        config()->set('seo.default_indexable', true);
        $this->assertFalse(CatalogPreview::enabled());
        $this->get(route('catalog'))->assertDontSee($product->sku);
        $this->get(route('product.show', $product->slug))->assertNotFound();
    }

    public function test_preview_can_be_switched_off_without_changing_database(): void
    {
        $category = $this->category(true);
        $product = $this->draft($category, 850000);
        $product->update(['price_on_request' => false]);
        $this->get(route('catalog'))->assertSee('class="product-card-price">0 <span', false);

        config()->set('catalog.preview_enabled', false);
        $this->assertFalse(CatalogPreview::enabled());
        $this->get(route('catalog'))->assertDontSee($product->sku);
        $this->assertSame(850000, $product->fresh()->price_gnf);

        $product->update(['is_active' => true]);
        $this->get(route('product.show', $product->slug))->assertOk()
            ->assertSee('850 000')
            ->assertDontSee('0 GNF');
    }

    public function test_locally_staged_technical_rows_become_idempotent_preview_drafts(): void
    {
        $category = $this->category(false);
        $batch = CatalogImportBatch::create([
            'total_rows' => 2,
            'status' => 'review',
        ]);
        $ready = $batch->rows()->create([
            'source_row' => 3,
            'source_category' => 'IP Cameras',
            'suggested_category_id' => $category->id,
            'external_model' => 'DH-IPC-TEST-003',
            'description' => 'Caméra IP test issue des données techniques.',
            'features' => 'Fiche technique d’essai, sans prix fournisseur.',
            'status' => CatalogImportRow::PENDING,
        ]);
        $batch->rows()->create([
            'source_row' => 4,
            'source_category' => 'IP Cameras',
            'suggested_category_id' => $category->id,
            'external_model' => null,
            'description' => 'Ligne non admissible.',
            'status' => CatalogImportRow::INVALID,
        ]);

        $this->actingAs($this->admin())->post(route('admin.preview.prepare'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseCount('products', 1);
        $product = Product::firstOrFail();
        $this->assertSame('DH-IPC-TEST-003', $product->sku);
        $this->assertFalse($product->is_active);
        $this->assertNull($product->price_gnf);
        $this->assertSame(0, $product->stock_qty);
        $this->assertSame(CatalogImportRow::APPROVED, $ready->fresh()->status);

        $this->get(route('catalog'))->assertOk()->assertSee('DH-IPC-TEST-003')->assertSee('class="product-card-price">0 <span', false);
        $this->get(route('category.show', $category))->assertOk()->assertSee('DH-IPC-TEST-003');

        $this->post(route('admin.preview.prepare'))->assertRedirect();
        $this->assertDatabaseCount('products', 1);
        $this->assertSame(0, app(CatalogPreviewPreparer::class)->counts()['pending']);
    }

    public function test_preview_bulk_action_is_admin_only_and_unavailable_outside_localhost(): void
    {
        $this->post(route('admin.preview.prepare'))->assertRedirect(route('login'));

        $nonAdmin = User::create([
            'name' => 'Visiteur',
            'email' => 'nonadmin@example.test',
            'password' => 'nonadmin-password-123',
            'is_admin' => false,
        ]);
        $this->actingAs($nonAdmin)->post(route('admin.preview.prepare'))->assertForbidden();

        $this->actingAs($this->admin());
        config()->set('app.url', 'https://shop.example.test');
        $this->post(route('admin.preview.prepare'))->assertNotFound();
    }

    public function test_local_admin_has_one_click_preview_control_and_safe_cli(): void
    {
        $this->draft($this->category());
        $this->actingAs($this->admin())->get(route('admin.products.index'))->assertOk()
            ->assertSee('Aperçu local actif')
            ->assertSee('0 GNF');

        $this->artisan('catalogue:preparer-apercu', ['--dry-run' => true])->assertExitCode(0);
        $this->assertDatabaseCount('products', 1);
    }
}
