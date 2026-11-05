<?php

namespace Tests\Feature;

use App\Mail\QuoteReceivedMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\QuoteRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PrelaunchCommercialTest extends TestCase
{
    use RefreshDatabase;

    private function admin(bool $allowed = true): User
    {
        return User::create([
            'name' => 'Agent de test commercial',
            'email' => $allowed ? 'agent@example.test' : 'visiteur@example.test',
            'password' => 'long-password-test-123',
            'is_admin' => $allowed,
        ]);
    }

    private function product(bool $categoryEnabled = true): Product
    {
        $category = Category::create([
            'name' => 'Caméras réseau fictives',
            'slug' => 'cameras-reseau-test',
            'is_active' => $categoryEnabled,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'sku' => 'EXAMPLE-OFFLINE-CAM',
            'slug' => 'example-offline-cam',
            'name' => 'Produit fictif pour recette',
            'price_gnf' => 200000,
            'stock_qty' => 5,
            'is_active' => true,
        ]);
    }

    private function quoteData(?Product $product = null): array
    {
        return [
            'product_id' => $product?->id,
            'name' => 'Demandeur de test',
            'phone' => '+224 610 000 111',
            'email' => 'demandeur@example.test',
            'quantity' => 2,
            'message' => 'Merci de confirmer les références en question.',
            'consent' => '1',
        ];
    }

    public function test_quote_notifies_sales_team_and_customer_without_online_payment(): void
    {
        Mail::fake();
        config()->set('commerce.quote_alert_email', 'devis@example.test');
        $product = $this->product();

        $this->post(route('quote.store'), $this->quoteData($product))
            ->assertRedirect(route('quote.create'));

        $this->assertDatabaseCount('quote_requests', 1);
        $quote = QuoteRequest::firstOrFail();
        $this->assertSame(QuoteRequest::NEW, $quote->status);
        $this->assertNotNull($quote->consented_at);
        Mail::assertSent(QuoteReceivedMail::class, 2);
        Mail::assertSent(QuoteReceivedMail::class, fn (QuoteReceivedMail $mail) =>
            $mail->forTeam && $mail->hasTo('devis@example.test'));
        Mail::assertSent(QuoteReceivedMail::class, fn (QuoteReceivedMail $mail) =>
            ! $mail->forTeam && $mail->hasTo('demandeur@example.test'));

        $this->assertFalse(config('commerce.online_payments_enabled'));
    }

    public function test_quote_without_customer_email_only_notifies_configured_team(): void
    {
        Mail::fake();
        config()->set('commerce.quote_alert_email', 'devis@example.test');
        $data = $this->quoteData();
        unset($data['email']);

        $this->post(route('quote.store'), $data)->assertRedirect();
        Mail::assertSent(QuoteReceivedMail::class, 1);
        Mail::assertSent(QuoteReceivedMail::class, fn (QuoteReceivedMail $mail) => $mail->forTeam);
    }

    public function test_unpublished_category_product_cannot_receive_a_quote(): void
    {
        Mail::fake();
        $product = $this->product(false);
        $this->post(route('quote.store'), $this->quoteData($product))
            ->assertSessionHasErrors('product_id');
        $this->assertDatabaseCount('quote_requests', 0);
        Mail::assertNothingSent();
    }

    public function test_honeypot_and_missing_consent_reject_quote_without_notifications(): void
    {
        Mail::fake();
        $this->post(route('quote.store'), array_merge($this->quoteData(), [
            'website' => 'automated-spam-test',
        ]))->assertSessionHasErrors('website');
        $data = $this->quoteData();
        unset($data['consent']);
        $this->post(route('quote.store'), $data)->assertSessionHasErrors('consent');
        $this->assertDatabaseCount('quote_requests', 0);
        Mail::assertNothingSent();
    }

    public function test_admin_followup_is_protected_and_audited(): void
    {
        Mail::fake();
        $this->post(route('quote.store'), $this->quoteData())->assertRedirect();
        $quote = QuoteRequest::firstOrFail();

        $this->patch(route('admin.quotes.update', $quote), [
            'status' => QuoteRequest::IN_PROGRESS,
        ])->assertRedirect(route('login'));

        $this->actingAs($this->admin(false))
            ->patch(route('admin.quotes.update', $quote), [
                'status' => QuoteRequest::IN_PROGRESS,
            ])->assertForbidden();

        $this->actingAs($this->admin())
            ->patch(route('admin.quotes.update', $quote), [
                'status' => QuoteRequest::IN_PROGRESS,
                'internal_note' => 'Appeler le prospect et confirmer les quantités.',
            ])->assertRedirect();

        $quote->refresh();
        $this->assertSame(QuoteRequest::IN_PROGRESS, $quote->status);
        $this->assertNotNull($quote->reviewed_at);
        $this->assertDatabaseHas('quote_request_events', [
            'quote_request_id' => $quote->id,
            'from_status' => QuoteRequest::NEW,
            'to_status' => QuoteRequest::IN_PROGRESS,
        ]);
        $this->get(route('admin.quotes.index'))->assertOk()->assertSee('Historique des actions');
    }

    public function test_quote_cannot_reopen_closed_status_and_unchanged_status_requires_note(): void
    {
        $quote = QuoteRequest::create(array_merge($this->quoteData(), [
            'status' => QuoteRequest::CLOSED,
            'consented_at' => now(),
        ]));
        $this->actingAs($this->admin());

        $this->patch(route('admin.quotes.update', $quote), [
            'status' => QuoteRequest::NEW,
        ])->assertSessionHasErrors('status');

        $this->patch(route('admin.quotes.update', $quote), [
            'status' => QuoteRequest::CLOSED,
        ])->assertSessionHasErrors('internal_note');

        $this->assertDatabaseCount('quote_request_events', 0);
    }

    public function test_quote_only_products_are_rejected_at_each_cart_step(): void
    {
        $product = $this->product();
        // Simulate a legacy import containing both a stale price and quote-only flag.
        $product->update(['price_on_request' => true]);

        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertSessionHasErrors('cart');

        $this->withSession(['cart' => [$product->id => 2]])
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('plus disponible');

        $this->withSession(['cart' => [$product->id => 2]])
            ->post(route('cart.update'), [
                'product_id' => $product->id,
                'quantity' => 1,
            ])->assertSessionHasErrors('cart');

        $this->withSession([
            'cart' => [$product->id => 1],
            'checkout_token' => 'd97ed29d-8395-4d1c-bac5-9dca548d760a',
        ])->post(route('checkout.store'), [
            'checkout_token' => 'd97ed29d-8395-4d1c-bac5-9dca548d760a',
            'customer_name' => 'Client Test',
            'customer_phone' => '+224 610 000 001',
            'city' => 'Conakry',
            'address' => 'Adresse fictive',
            'consent' => '1',
        ])->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_security_headers_and_no_referrer_on_public_pages(): void
    {
        $this->get(route('home'))
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader('Content-Security-Policy', "frame-ancestors 'none'");
    }

    public function test_readiness_dashboard_and_preflight_do_not_leak_config_secrets(): void
    {
        $this->get(route('admin.launch-readiness'))->assertRedirect(route('login'));
        $this->actingAs($this->admin(false))
            ->get(route('admin.launch-readiness'))->assertForbidden();

        config()->set('mail.mailers.smtp.password', 'CONFIDENTIAL-TEST-SECRET-123');
        $this->actingAs($this->admin())
            ->get(route('admin.launch-readiness'))->assertOk()
            ->assertSee('Préparation de la mise en ligne')
            ->assertDontSee('CONFIDENTIAL-TEST-SECRET-123');

        $this->assertSame(1, Artisan::call('commerce:preflight', ['--strict' => true]));
        $this->assertStringNotContainsString('CONFIDENTIAL-TEST-SECRET-123', Artisan::output());
    }

    public function test_legal_templates_are_marked_draft_until_explicitly_reviewed(): void
    {
        config()->set('site.privacy_approved', false);
        config()->set('site.sales_approved', false);

        $this->get(route('legal.privacy'))->assertOk()->assertSee('noindex,follow');
        $this->get(route('legal.sales'))->assertOk()->assertSee('noindex,follow');
        $this->get(route('legal.mentions'))->assertOk()->assertSee('Mentions légales');

        $this->get(route('quote.create'))->assertOk()
            ->assertSee('politique de confidentialité');
    }
}
