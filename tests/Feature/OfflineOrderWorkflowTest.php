<?php

namespace Tests\Feature;

use App\Mail\OrderStatusMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class OfflineOrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function product(int $stock = 10): Product
    {
        $category = Category::create(['name' => 'Caméras IP', 'slug' => 'cameras-ip', 'is_active' => true]);

        return Product::create([
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'sku' => 'SKU-OFFLINE-TEST-01',
            'slug' => 'sku-offline-test-01',
            'name' => 'Référence de test hors catalogue réel',
            'price_gnf' => 250000,
            'stock_qty' => $stock,
            'is_active' => true,
        ]);
    }

    private function buyer(string $token): array
    {
        return [
            'checkout_token' => $token,
            'customer_name' => 'Cliente Test',
            'customer_phone' => '+224 620 000 000',
            'customer_email' => 'cliente@example.test',
            'city' => 'Conakry',
            'address' => 'Adresse fictive de test',
            'consent' => '1',
        ];
    }

    private function submit(Product $product, int $qty = 2): Order
    {
        $token = (string) Str::uuid();
        $this->withSession([
            'cart' => [$product->id => $qty],
            'checkout_token' => $token,
        ])->post(route('checkout.store'), $this->buyer($token))
            ->assertRedirect(route('checkout.success'));

        return Order::firstOrFail();
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Responsable Commercial',
            'email' => 'responsable@example.test',
            'password' => 'long-test-password-123',
            'is_admin' => true,
        ]);
    }

    public function test_checkout_page_creates_session_token(): void
    {
        $product = $this->product();
        $this->withSession(['cart' => [$product->id => 1]])
            ->get(route('checkout.create'))
            ->assertOk()
            ->assertSessionHas('checkout_token')
            ->assertSee('Aucun paiement');
    }

    public function test_checkout_rejects_missing_or_incorrect_token(): void
    {
        $product = $this->product();
        $token = (string) Str::uuid();
        $this->withSession(['cart' => [$product->id => 1], 'checkout_token' => $token])
            ->post(route('checkout.store'), $this->buyer((string) Str::uuid()))
            ->assertSessionHasErrors('checkout_token');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_repeated_checkout_with_same_token_creates_one_order_only(): void
    {
        Mail::fake();
        $product = $this->product();
        $token = (string) Str::uuid();
        $session = ['cart' => [$product->id => 2], 'checkout_token' => $token];

        $this->withSession($session)->post(route('checkout.store'), $this->buyer($token))
            ->assertRedirect(route('checkout.success'));

        $this->withSession($session)->post(route('checkout.store'), $this->buyer($token))
            ->assertRedirect(route('checkout.success'));

        $this->assertDatabaseCount('orders', 1);
        Mail::assertSent(OrderStatusMail::class, 1);
    }

    public function test_customer_and_team_are_emailed_only_with_configured_recipients(): void
    {
        Mail::fake();
        config()->set('commerce.order_alert_email', 'commandes@example.test');
        $order = $this->submit($this->product());

        Mail::assertSent(OrderStatusMail::class, 2);
        Mail::assertSent(OrderStatusMail::class, function (OrderStatusMail $mail) use ($order) {
            return ! $mail->forTeam && $mail->order->is($order);
        });
        Mail::assertSent(OrderStatusMail::class, function (OrderStatusMail $mail) {
            return $mail->forTeam;
        });
    }

    public function test_private_tracking_requires_valid_signed_link(): void
    {
        Mail::fake();
        $order = $this->submit($this->product());
        $url = app(OrderNotifier::class)->trackingUrl($order);

        $this->get(route('order.track', $order))->assertForbidden();
        $this->get($url)->assertOk()
            ->assertSee($order->reference)
            ->assertSee('En attente de validation')
            ->assertDontSee($order->customer_phone)
            ->assertDontSee($order->address);
    }

    public function test_confirm_requires_prior_reservation_and_validated_shipping(): void
    {
        Mail::fake();
        $product = $this->product(stock: 6);
        $order = $this->submit($product);
        $this->actingAs($this->admin());

        $this->patch(route('admin.orders.confirm', $order), ['shipping_gnf' => 75000])
            ->assertSessionHasErrors('order');

        $this->patch(route('admin.orders.reserve', $order))->assertRedirect();
        $this->assertSame(4, $product->fresh()->stock_qty);

        $this->patch(route('admin.orders.confirm', $order), ['shipping_gnf' => -1])
            ->assertSessionHasErrors('shipping_gnf');

        $this->patch(route('admin.orders.confirm', $order), [
            'shipping_gnf' => 75000,
            'internal_note' => 'Tarif de livraison approuvé par le conseiller.',
        ])->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::CONFIRMED, $order->status);
        $this->assertSame(575000, $order->total_gnf);
        $this->assertSame(75000, $order->shipping_gnf);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame(4, $product->fresh()->stock_qty);
        $this->assertDatabaseCount('order_events', 3);

        $this->patch(route('admin.orders.confirm', $order), ['shipping_gnf' => 100000])
            ->assertSessionHasErrors('order');
        $this->patch(route('admin.orders.cancel', $order))
            ->assertSessionHasErrors('order');
        $this->assertSame(4, $product->fresh()->stock_qty);
    }

    public function test_status_changes_trigger_email_without_online_payment(): void
    {
        Mail::fake();
        $order = $this->submit($this->product());

        $this->actingAs($this->admin())->patch(route('admin.orders.reserve', $order));
        $this->patch(route('admin.orders.confirm', $order), ['shipping_gnf' => 0]);

        Mail::assertSent(OrderStatusMail::class, 3);
        $this->assertSame('unpaid', $order->fresh()->payment_status);
    }
}
