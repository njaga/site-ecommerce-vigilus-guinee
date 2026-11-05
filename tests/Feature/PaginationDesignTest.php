<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationDesignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.url', 'http://127.0.0.1:8000');
        config()->set('catalog.preview_enabled', true);
        config()->set('seo.default_indexable', false);
    }

    private function populateCatalog(int $count = 57): void
    {
        $category = Category::create([
            'name' => 'Caméras',
            'slug' => 'cameras-pagination',
            'is_active' => true,
        ]);

        for ($i = 1; $i <= $count; $i++) {
            Product::create([
                'category_id' => $category->id,
                'brand' => 'Dahua',
                'sku' => sprintf('PAG-TEST-%03d', $i),
                'slug' => sprintf('camera-pagination-%03d', $i),
                'name' => sprintf('Camera Dahua pour pagination %03d', $i),
                'short_description' => 'Référence de test de la navigation.',
                'price_gnf' => null,
                'price_on_request' => true,
                'stock_qty' => 0,
                'is_active' => false,
            ]);
        }
    }

    public function test_store_catalog_uses_compact_french_pagination_and_retains_search(): void
    {
        $this->populateCatalog();

        $this->get(route('catalog', ['q' => 'Camera']))->assertOk()
            ->assertSee('class="v-pagination"', false)
            ->assertSee('1–24 sur 57')
            ->assertSee('Précédent')
            ->assertSee('Suivant')
            ->assertSee('aria-current="page"', false)
            ->assertSee('q=Camera', false)
            ->assertDontSee('pagination.previous')
            ->assertDontSee('pagination.next');

        $this->get(route('catalog', ['q' => 'Camera', 'page' => 2]))->assertOk()
            ->assertSee('25–48 sur 57')
            ->assertSee('rel="prev"', false)
            ->assertSee('rel="next"', false)
            ->assertSee('q=Camera', false);

        $this->get(route('catalog', ['q' => 'Camera', 'page' => 3]))->assertOk()
            ->assertSee('49–57 sur 57')
            ->assertSee('aria-disabled="true"', false);
    }

    public function test_admin_products_share_the_same_numbered_pagination(): void
    {
        $this->populateCatalog();

        $admin = User::create([
            'name' => 'Pagination Admin',
            'email' => 'pagination@example.test',
            'password' => 'strong-password-123',
            'is_admin' => true,
        ]);

        $this->actingAs($admin)->get(route('admin.products.index', [
            'q' => 'Camera',
            'statut' => 'draft',
            'page' => 2,
        ]))->assertOk()
            ->assertSee('class="v-pagination"', false)
            ->assertSee('26–50 sur 57')
            ->assertSee('aria-current="page"', false)
            ->assertSee('Précédent')
            ->assertSee('Suivant')
            ->assertSee('statut=draft', false)
            ->assertSee('q=Camera', false)
            ->assertDontSee('pagination.previous')
            ->assertDontSee('pagination.next');
    }

    public function test_single_page_has_no_pagination_controls(): void
    {
        $this->populateCatalog(4);

        $this->get(route('catalog'))->assertOk()
            ->assertDontSee('class="v-pagination"', false);
    }
}
