<?php

namespace Tests\Feature;

use App\Support\ContactChannels;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_contact_page_uses_city_and_does_not_link_an_incomplete_whatsapp_number(): void
    {
        config()->set(['site.address' => '', 'site.whatsapp' => '+224', 'services.google_maps.embed_api_key' => null, 'services.google_maps.query' => null]);
        $this->get(route('contact'))->assertOk()
            ->assertSee('Parlons de votre projet.')
            ->assertSee('Conakry, Guinée')
            ->assertSee('Localisation indicative')
            ->assertSee('https://www.google.com/maps/search/', false)
            ->assertSee(route('quote.create'))->assertSee(route('training.index'))
            ->assertDontSee('https://wa.me/', false)
            ->assertDontSee('maps/embed/v1/', false);
    }

    public function test_configured_map_encodes_its_query_and_whatsapp_uses_a_complete_number(): void
    {
        config()->set([
            'site.address' => 'Adresse de test', 'site.whatsapp' => '+224 610 000 001',
            'services.google_maps.embed_api_key' => 'test-browser-key',
            'services.google_maps.query' => 'Conakry & environs',
        ]);
        $channels = app(ContactChannels::class);
        parse_str(parse_url($channels->mapEmbedUrl(), PHP_URL_QUERY), $map);
        $this->assertSame('test-browser-key', $map['key']);
        $this->assertSame('Conakry & environs', $map['q']);
        $this->assertSame('https://wa.me/224610000001', strtok($channels->whatsappUrl(), '?'));
        $this->get(route('contact'))->assertOk()
            ->assertSee('maps/embed/v1/place?', false)
            ->assertSee('referrerpolicy="strict-origin-when-cross-origin"', false)
            ->assertSee('https://wa.me/224610000001?', false)
            ->assertSee('Adresse de test');
    }

    public function test_invalid_whatsapp_values_never_become_outbound_links(): void
    {
        foreach (['', '+224', 'javascript:224610000001', '+000000000', '+2241234567890123456'] as $number) {
            config()->set('site.whatsapp', $number);
            $this->assertNull(app(ContactChannels::class)->whatsappUrl());
        }
    }
}
