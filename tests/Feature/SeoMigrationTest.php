<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\SeoRedirect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SeoMigrationTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(bool $admin = true): User
    {
        return User::create([
            'name' => $admin ? 'Responsable migration' : 'Visiteur',
            'email' => $admin ? 'migration@example.test' : 'nonadmin@example.test',
            'password' => 'test-password-longue-456',
            'is_admin' => $admin,
        ]);
    }

    private function publicProduct(): Product
    {
        $category = Category::create([
            'name' => 'Caméras IP',
            'slug' => 'cameras-ip',
            'is_active' => true,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'sku' => 'FAKE-SEO-01',
            'slug' => 'camera-dahua-test',
            'name' => 'Référence test non commerciale',
            'short_description' => 'Modèle fictif pour la migration SEO',
            'stock_qty' => 0,
            'is_active' => true,
        ]);
    }

    private function mapping(string $old, string $new, bool $active = true, string $host = 'localhost'): SeoRedirect
    {
        return SeoRedirect::create([
            'source_host' => $host,
            'source_path' => $old,
            'destination_path' => $new,
            'review_note' => 'Correspondance de test pour le même marché après vérification.',
            'is_active' => $active,
        ]);
    }

    public function test_unknown_historical_url_returns_404_instead_of_redirecting_home(): void
    {
        $this->get('/ancienne-page-inconnue')->assertNotFound();
    }

    public function test_redirects_are_admin_only_and_created_inactive(): void
    {
        $this->get(route('admin.seo-redirects.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($this->administrator(false))
            ->get(route('admin.seo-redirects.index'))->assertForbidden();

        $this->actingAs($this->administrator())->post(route('admin.seo-redirects.store'), [
            'source_host' => 'localhost',
            'source_path' => '/ancienne-fiche',
            'destination_path' => '/produits',
            'review_note' => 'Même marché, section catalogue réellement équivalente.',
            'confirmed_same_market' => '1',
        ])->assertRedirect();

        $rule = SeoRedirect::firstOrFail();
        $this->assertSame('/ancienne-fiche', $rule->source_path);
        $this->assertFalse($rule->is_active);
        $this->get('/ancienne-fiche')->assertNotFound();
    }

    public function test_activation_301_for_a_valid_published_product_even_on_dynamic_legacy_product_route(): void
    {
        $product = $this->publicProduct();
        $rule = $this->mapping('/produits/ancien-modele', '/produits/'.$product->slug, false);
        $this->actingAs($this->administrator())
            ->patch(route('admin.seo-redirects.activate', $rule))->assertRedirect();

        $this->assertTrue($rule->fresh()->is_active);
        $this->get('/produits/ancien-modele?utm_campaign=historique')
            ->assertStatus(301)
            ->assertHeader('Location', route('product.show', $product->slug));
    }

    public function test_disabled_or_unavailable_destinations_do_not_redirect(): void
    {
        $product = $this->publicProduct();
        $rule = $this->mapping('/ancienne-camera', '/produits/'.$product->slug, false);

        $this->get('/ancienne-camera')->assertNotFound();
        $rule->update(['is_active' => true]);
        $this->get('/ancienne-camera')->assertStatus(301);
        $product->update(['is_active' => false]);
        $this->get('/ancienne-camera')->assertNotFound();
    }

    public function test_cannot_activate_a_rule_without_a_real_public_destination(): void
    {
        $rule = $this->mapping('/ancienne-fiche', '/produits/future-ref', false);
        $this->actingAs($this->administrator())
            ->patch(route('admin.seo-redirects.activate', $rule))
            ->assertSessionHasErrors('redirect');

        $this->assertFalse($rule->fresh()->is_active);
    }

    public function test_other_country_domain_is_not_allowed_by_default(): void
    {
        $this->actingAs($this->administrator())->post(route('admin.seo-redirects.store'), [
            'source_host' => 'groupevigilus.ci',
            'source_path' => '/services/videosurveillance-securite',
            'destination_path' => '/equipements-videosurveillance-conakry',
            'review_note' => 'Cette cartographie est volontairement invalide.',
            'confirmed_same_market' => '1',
        ])->assertSessionHasErrors('source_host');
        $this->assertDatabaseCount('seo_redirects', 0);

        $this->mapping('/ancien-produit-ivoirien', '/produits', true, 'groupevigilus.ci');
        $this->get('http://groupevigilus.ci/ancien-produit-ivoirien')->assertNotFound();
    }

    public function test_rejects_external_target_private_paths_open_redirects_and_loops(): void
    {
        $this->actingAs($this->administrator());
        foreach ([
            ['//evil.example', 'destination_path'],
            ['/admin/produits', 'destination_path'],
            ['/panier', 'destination_path'],
            ['/produits/old?external=1', 'destination_path'],
        ] as [$destination, $field]) {
            $this->post(route('admin.seo-redirects.store'), [
                'source_host' => 'localhost',
                'source_path' => '/historique',
                'destination_path' => $destination,
                'review_note' => 'Une justification de correspondance pour test.',
                'confirmed_same_market' => '1',
            ])->assertSessionHasErrors($field);
        }
        $this->post(route('admin.seo-redirects.store'), [
            'source_host' => 'localhost',
            'source_path' => '/produits',
            'destination_path' => '/equipements-videosurveillance-conakry',
            'review_note' => 'Chemin historique protégé par le nouveau site.',
            'confirmed_same_market' => '1',
        ])->assertSessionHasErrors('source_path');

        $this->post(route('admin.seo-redirects.store'), [
            'source_host' => 'localhost',
            'source_path' => '/produits/ancienne-reference',
            'destination_path' => '/produits/ancienne-reference',
            'review_note' => 'Une boucle de redirection artificielle.',
            'confirmed_same_market' => '1',
        ])->assertSessionHasErrors('source_path');

        $this->assertDatabaseCount('seo_redirects', 0);
    }

    public function test_redirects_do_not_override_existing_live_product_urls(): void
    {
        $product = $this->publicProduct();
        $this->actingAs($this->administrator())->post(route('admin.seo-redirects.store'), [
            'source_host' => 'localhost',
            'source_path' => '/produits/'.$product->slug,
            'destination_path' => '/produits',
            'review_note' => 'Interdiction de détourner un produit existant.',
            'confirmed_same_market' => '1',
        ])->assertSessionHasErrors('source_path');
    }

    public function test_redirects_apply_only_to_get_and_head_not_post(): void
    {
        $this->mapping('/ancien-chemin', '/produits', true);
        $this->get('/ancien-chemin')->assertStatus(301);
        $this->post('/ancien-chemin')->assertNotFound();
    }

    public function test_rule_can_be_deactivated_but_not_deleted_while_active(): void
    {
        $rule = $this->mapping('/ancienne-fiche', '/produits', true);
        $this->actingAs($this->administrator())
            ->delete(route('admin.seo-redirects.destroy', $rule))
            ->assertSessionHasErrors('redirect');

        $this->patch(route('admin.seo-redirects.deactivate', $rule))->assertRedirect();
        $this->get('/ancienne-fiche')->assertNotFound();

        $this->delete(route('admin.seo-redirects.destroy', $rule))->assertRedirect();
        $this->assertDatabaseCount('seo_redirects', 0);
    }

    public function test_stage_is_not_indexed_even_if_individual_view_claims_indexable(): void
    {
        config()->set('seo.default_indexable', false);
        $this->get('/')->assertOk()->assertSee('content="noindex,nofollow"', false);
        $this->get(route('solution.videosurveillance'))
            ->assertOk()->assertSee('content="noindex,nofollow"', false);

        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
        $this->get('/sitemap.xml')->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertDontSee('/equipements-videosurveillance-conakry');
    }

    public function test_old_installation_url_redirects_to_distribution_page(): void
    {
        $this->get('/installation-videosurveillance-conakry')
            ->assertStatus(301)
            ->assertHeader('Location', '/equipements-videosurveillance-conakry');

        $this->get('/equipements-videosurveillance-conakry')
            ->assertOk()->assertSee('Le journal Vigilus');
    }

    public function test_cli_audit_flags_unavailable_active_redirects(): void
    {
        $this->mapping('/ancien-produit', '/produits/produit-non-public', true);
        $exit = Artisan::call('seo:audit-redirects', ['--strict' => true]);
        $this->assertSame(1, $exit);
    }
}
