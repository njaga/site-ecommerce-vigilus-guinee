<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogPublishingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Éditeur du catalogue',
            'email' => 'editeur@example.test',
            'password' => 'long-test-password-123',
            'is_admin' => true,
        ]);
    }

    private function category(bool $active = true): Category
    {
        return Category::create([
            'name' => 'Caméras IP',
            'slug' => 'cameras-ip',
            'is_active' => $active,
        ]);
    }

    private function draft(Category $category): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'name' => 'Modèle de test',
            'sku' => 'SKU-FAKE-TEST',
            'slug' => 'sku-fake-test',
            'is_active' => false,
            'stock_qty' => 0,
        ]);
    }

    private function completeData(Category $category, array $overrides = []): array
    {
        return array_merge([
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'sku' => 'SKU-FAKE-TEST',
            'name' => 'Caméra de test pour validation du catalogue',
            'short_description' => 'Description entièrement fictive de ce modèle de test, réservée aux essais du catalogue.',
            'description' => 'Cette description commerciale synthétique explique les usages et les précautions de ce modèle fictif, sans reprendre de prix, de garantie ni de données confidentielles provenant du fournisseur.',
            'specifications_text' => "Élément de test : valeur fictive\nAutre élément : autre valeur fictive",
            'seo_title' => 'Caméra Dahua de test à Conakry | Vigilus Guinée',
            'seo_description' => 'Cette référence fictive illustre la gestion éditoriale du catalogue de sécurité électronique à Conakry, sans offre commerciale réelle.',
            'price_gnf' => 450000,
            'stock_qty' => 5,
            'technical_verified' => 1,
            'commercial_verified' => 1,
            'media_rights_verified' => 1,
            'is_active' => 1,
            'image' => UploadedFile::fake()->image('modele-test.jpg', 640, 480),
            'image_alt' => 'Caméra de test synthétique utilisée pour les essais internes',
        ], $overrides);
    }

    public function test_draft_editor_lists_publication_blockers_before_any_attempt(): void
    {
        $category = $this->category();
        $product = $this->draft($category);
        $this->actingAs($this->admin())
            ->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('À compléter avant publication')
            ->assertSee('Renseignez un prix de vente GNF validé')
            ->assertSee('Ajoutez une photo constructeur autorisée');

        $this->assertFalse($product->fresh()->is_active);
    }

    public function test_incomplete_boq_draft_cannot_be_published(): void
    {
        $category = $this->category();
        $product = $this->draft($category);
        $this->actingAs($this->admin())->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'sku' => $product->sku,
            'name' => $product->name,
            'stock_qty' => 0,
            'is_active' => 1,
        ])->assertSessionHasErrors([
            'short_description', 'description', 'specifications_text',
            'image', 'price_gnf', 'technical_verified',
            'commercial_verified', 'media_rights_verified',
        ]);

        $this->assertFalse($product->fresh()->is_active);
        $this->get(route('product.show', $product->slug))->assertNotFound();
    }

    public function test_publication_requires_active_category(): void
    {
        Storage::fake('public');
        $category = $this->category(false);
        $this->actingAs($this->admin())
            ->post(route('admin.products.store'), $this->completeData($category))
            ->assertSessionHasErrors('category_id');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_price_on_request_is_explicit_and_never_allows_cart(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $data = $this->completeData($category, [
            'sku' => 'FAKE-QUOTE-01',
            'price_gnf' => null,
            'price_on_request' => 1,
            'stock_qty' => 5,
        ]);
        $this->actingAs($this->admin())
            ->post(route('admin.products.store'), $data)->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'FAKE-QUOTE-01')->firstOrFail();
        $this->assertTrue($product->is_active);
        $this->assertTrue($product->price_on_request);
        $this->assertFalse($product->can_order);
        $this->get(route('product.show', $product->slug))
            ->assertOk()->assertSee('Prix sur devis')->assertDontSee('Ajouter au panier');
    }

    public function test_price_on_request_and_public_price_cannot_be_combined(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $this->actingAs($this->admin())->post(route('admin.products.store'),
            $this->completeData($category, ['price_on_request' => 1]))
            ->assertSessionHasErrors('price_gnf');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_published_product_needs_documented_photo(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $data = $this->completeData($category);
        unset($data['image']);

        $this->actingAs($this->admin())
            ->post(route('admin.products.store'), $data)
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_category_editor_updates_public_seo_metadata(): void
    {
        $category = $this->category();
        $this->actingAs($this->admin())->put(route('admin.categories.update', $category), [
            'name' => 'Caméras IP',
            'description' => 'Découvrez les catégories de caméras de surveillance à Conakry, avec conseil et devis.',
            'seo_title' => 'Caméras IP à Conakry : modèles Dahua | Vigilus Guinée',
            'seo_description' => 'Choisissez des caméras IP et des équipements de surveillance selon votre besoin à Conakry. Consultez les modèles Dahua et contactez Vigilus Guinée.',
            'sort_order' => 1,
            'is_active' => 1,
        ])->assertRedirect();

        $this->assertSame(
            'Caméras IP à Conakry : modèles Dahua | Vigilus Guinée',
            $category->fresh()->seo_title,
        );
    }

    public function test_empty_category_is_noindex_and_excluded_from_sitemap(): void
    {
        $category = $this->category();
        $this->get(route('category.show', $category))
            ->assertOk()->assertSee('noindex,follow');

        $this->get(route('sitemap'))
            ->assertOk()->assertDontSee('/categories/'.$category->slug);

        $this->get(route('solution.videosurveillance'))
            ->assertOk()->assertSee('Actualités');
    }

    public function test_image_management_cannot_modify_other_product_images(): void
    {
        $category = $this->category();
        $one = $this->draft($category);
        $two = Product::create([
            'category_id' => $category->id, 'brand' => 'Dahua', 'sku' => 'DIFFERENT-TEST',
            'slug' => 'different-test', 'name' => 'Autre modèle test',
            'stock_qty' => 0, 'is_active' => false,
        ]);
        $image = $one->images()->create([
            'path' => 'products/dummy-test.jpg',
            'alt' => 'Image de test appartenant au premier modèle',
            'sort_order' => 0,
        ]);
        $this->actingAs($this->admin())
            ->patch(route('admin.products.images.update', [$two, $image]), [
                'alt' => 'Photographie test utilisée pour le second produit',
                'sort_order' => 1,
            ])->assertNotFound();

        $this->assertSame(0, $image->fresh()->sort_order);
    }

    public function test_cannot_delete_last_photo_from_published_product(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $product = $this->draft($category);
        $product->update(['is_active' => true]);

        $image = $product->images()->create([
            'path' => 'products/last-photo.jpg',
            'alt' => 'Image synthétique de caméra pour test de suppression',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put('products/last-photo.jpg', 'fake bytes');

        $this->actingAs($this->admin())
            ->delete(route('admin.products.images.destroy', [$product, $image]))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseHas('product_images', ['id' => $image->id]);
        Storage::disk('public')->assertExists('products/last-photo.jpg');

        $this->patch(route('admin.products.archive', $product))->assertRedirect();
        $this->delete(route('admin.products.images.destroy', [$product, $image]))->assertRedirect();

        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('products/last-photo.jpg');
    }
}
