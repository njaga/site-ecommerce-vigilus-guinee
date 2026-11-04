<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\QuoteRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommerceTest extends TestCase
{
    use RefreshDatabase;

    private function category(): Category
    {
        return Category::create(['name' => 'Caméras IP', 'slug' => 'cameras-ip', 'is_active' => true]);
    }

    private function product(Category $category, bool $active = true): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'sku' => 'EXEMPLE-TEST-001',
            'slug' => 'camera-exemple-test-001',
            'name' => 'Caméra de test (non commerciale)',
            'short_description' => 'Référence uniquement utilisée dans les tests automatisés.',
            'price_gnf' => null,
            'stock_qty' => 0,
            'is_active' => $active,
        ]);
    }

    public function test_home_and_empty_catalog_are_available(): void
    {
        $this->get('/')->assertOk()->assertSee('Votre sécurité commence');
        $this->get('/produits')->assertOk()->assertSee('Pas encore de référence');
    }

    public function test_unpublished_product_is_not_public_and_not_in_sitemap(): void
    {
        $product = $this->product($this->category(), false);
        $this->get('/produits/'.$product->slug)->assertNotFound();
        $this->get('/sitemap.xml')->assertOk()->assertDontSee($product->slug);
    }

    public function test_published_product_without_price_is_quote_only(): void
    {
        $product = $this->product($this->category());
        $this->get('/produits/'.$product->slug)->assertOk()->assertSee('Prix sur demande')->assertDontSee('0 GNF');
        $this->get('/sitemap.xml')->assertOk()->assertSee($product->slug);
    }

    public function test_quote_is_persisted_with_consent(): void
    {
        $this->post('/demande-de-devis', [
            'name' => 'Client Exemple',
            'phone' => '+224 600 000 000',
            'quantity' => 3,
            'message' => 'Demande pour une boutique.',
            'consent' => '1',
        ])->assertRedirect('/demande-de-devis');
        $this->assertDatabaseCount('quote_requests', 1);
        $this->assertNotNull(QuoteRequest::first()->consented_at);
    }

    public function test_quote_requires_consent(): void
    {
        $this->post('/demande-de-devis', [
            'name' => 'Client Exemple',
            'phone' => '600000000',
            'quantity' => 1,
        ])->assertSessionHasErrors('consent');
        $this->assertDatabaseCount('quote_requests', 0);
    }

    public function test_guest_and_non_admin_cannot_manage_catalog(): void
    {
        $this->get('/admin/produits')->assertRedirect(route('login'));
        $user = User::create(['name' => 'Utilisateur', 'email' => 'user@example.test', 'password' => 'secret-test-456', 'is_admin' => false]);
        $this->actingAs($user)->get('/admin/produits')->assertForbidden();
    }

    public function test_admin_can_create_published_product_with_price(): void
    {
        $category = $this->category();
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'secret-test-456', 'is_admin' => true]);
        Storage::fake('public');
        $this->actingAs($admin)->post('/admin/produits', [
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'sku' => 'TEST-IP-4MP',
            'name' => 'Référence de test 4 MP',
            'short_description' => 'Une description de test suffisamment détaillée pour représenter le modèle et son usage.',
            'description' => 'Cette description entièrement fictive sert à tester la validation éditoriale du catalogue Dahua, sans fournir de données techniques non vérifiées ni de caractéristiques commerciales réelles.',
            'specifications_text' => "Résolution : exemple non commercial\nAlimentation : exemple non commercial",
            'seo_title' => 'Caméra de test 4 MP à Conakry | Boutique Vigilus Guinée',
            'seo_description' => 'Référence fictive utilisée pour valider les pages de catalogue et leur référencement, sans annonce de produit ou stock réel au public.',
            'technical_verified' => 1,
            'commercial_verified' => 1,
            'media_rights_verified' => 1,
            'image' => UploadedFile::fake()->image('camera-test.jpg', 800, 600),
            'image_alt' => 'Photo synthétique de la caméra de test en laboratoire',
            'price_gnf' => 600000,
            'stock_qty' => 4,
            'is_active' => 1,
            'is_featured' => 1,
        ])->assertRedirect(route('admin.products.index'));
        $product = Product::where('sku', 'TEST-IP-4MP')->firstOrFail();
        $this->get(route('product.show', $product->slug))->assertOk()->assertSee('600 000');
    }
}
