<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupAndCurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_information_contacts_and_training_are_accessible(): void
    {
        config()->set('site.email', '');
        $this->get(route('home'))->assertOk()
            ->assertSee('Le représentant officiel exclusif')
            ->assertSee('Téléphone 1')->assertSee('Téléphone 2')
            ->assertSee('Adresse email à venir')
            ->assertSee('data-currency-select', false)
            ->assertSee('ISO 29001')->assertSee('ISO 45001')
            ->assertSee('images/flags/ae.svg');

        $this->get(route('about'))->assertOk()
            ->assertSee('Vigilus Properties')->assertSee('Vigilus Mobility')
            ->assertSee('Vigilus Facilities')->assertSee('Spear')
            ->assertSee('Toutes basées au Sénégal.')
            ->assertSee('Confection de tenues');

        $this->get(route('quote.create', ['sujet' => 'formation']))->assertOk()
            ->assertSee('Je souhaite être informé des prochaines formations Dahua');
    }

    public function test_checkout_shows_real_thumbnail_and_keeps_canonical_gnf_amounts(): void
    {
        $category = Category::create(['name' => 'Caméras IP', 'slug' => 'cameras-ip', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id, 'brand' => 'Dahua', 'sku' => 'CURRENCY-TEST',
            'slug' => 'currency-test', 'name' => 'Produit de test',
            'price_gnf' => 1000000, 'stock_qty' => 5, 'is_active' => true,
        ]);
        $product->images()->create(['path' => 'products/test-currency.jpg', 'alt' => 'Image de test', 'sort_order' => 0]);

        $this->withSession(['cart' => [$product->id => 2]])->get(route('checkout.create'))->assertOk()
            ->assertSee('equipment-thumbnail')
            ->assertSee('storage/products/test-currency.jpg')
            ->assertSee('data-price-gnf="1000000"', false)
            ->assertSee('data-price-gnf="2000000"', false)
            ->assertSee('2 000 000 GNF');
        $this->assertSame(1000000, $product->fresh()->price_gnf);
        $this->assertSame(5, $product->fresh()->stock_qty);

        $product->images()->delete();
        $this->get(route('checkout.create'))->assertOk()->assertSee('Visuel à venir');
    }

    public function test_configured_contacts_replace_placeholders_without_fake_links(): void
    {
        config()->set('site.phone', '+224 610 000 001');
        config()->set('site.phone_secondary', '+224 610 000 002');
        config()->set('site.email', 'test@example.test');
        config()->set('site.address', 'Adresse de test');
        $this->get(route('home'))->assertOk()
            ->assertSee('tel:+224610000001', false)->assertSee('tel:+224610000002', false)
            ->assertSee('mailto:test@example.test', false)->assertSee('Adresse de test')
            ->assertDontSee('+224 XXX XX XX XX');
    }
}
