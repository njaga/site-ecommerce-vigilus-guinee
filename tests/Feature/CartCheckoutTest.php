<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function item(int $stock = 6, ?int $price = 250000): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'cameras-ip'],
            ['name' => 'Caméras IP', 'is_active' => true],
        );
        return Product::create([
            'category_id' => $category->id,
            'brand' => 'Dahua',
            'sku' => 'SKU-TEST-100',
            'slug' => 'produit-test-100',
            'name' => 'Produit fictif de test, non publié en production',
            'price_gnf' => $price,
            'stock_qty' => $stock,
            'is_active' => true,
        ]);
    }

    private function customer(): array
    {
        return [
            'checkout_token' => 'd97ed29d-8395-4d1c-bac5-9dca548d760a',
            'customer_name' => 'Test Client',
            'customer_phone' => '+224 610 000 001',
            'customer_email' => 'client@example.test',
            'city' => 'Conakry',
            'district' => 'Ratoma',
            'address' => 'Adresse fictive uniquement pour test',
            'consent' => '1',
        ];
    }

    public function test_available_product_can_be_added_to_cart(): void
    {
        $product = $this->item();
        $this->post(route('cart.add'), ['product_id' => $product->id, 'quantity' => 2])
            ->assertRedirect(route('cart.index'))->assertSessionHas('cart.'.$product->id, 2);
        $this->get(route('cart.index'))->assertOk()->assertSee('500 000');
    }

    public function test_out_of_stock_and_unpriced_products_cannot_be_added(): void
    {
        $product = $this->item(stock: 0);
        $this->post(route('cart.add'), ['product_id' => $product->id, 'quantity' => 1])
            ->assertSessionHasErrors('cart');

        $product->update(['stock_qty' => 3, 'price_gnf' => null]);
        $this->post(route('cart.add'), ['product_id' => $product->id, 'quantity' => 1])
            ->assertSessionHasErrors('cart');

        $this->assertEmpty(session('cart', []));
    }

    public function test_checkout_creates_unpaid_request_without_decrementing_stock(): void
    {
        $product = $this->item();
        $this->withSession(['cart' => [$product->id => 2], 'checkout_token' => 'd97ed29d-8395-4d1c-bac5-9dca548d760a'])
            ->post(route('checkout.store'), $this->customer())
            ->assertRedirect(route('checkout.success'));

        $order = Order::firstOrFail();
        $this->assertSame('pending_review', $order->status);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame(500000, $order->subtotal_gnf);
        $this->assertNull($order->total_gnf);
        $this->assertNull($order->shipping_gnf);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id, 'quantity' => 2,
            'unit_price_gnf' => 250000, 'sku_snapshot' => $product->sku,
        ]);
        $this->assertSame(6, $product->fresh()->stock_qty);
        $this->assertEmpty(session('cart', []));
    }

    public function test_checkout_recalculates_price_on_server(): void
    {
        $product = $this->item();
        $product->update(['price_gnf' => 310000]);

        $this->withSession(['cart' => [$product->id => 3], 'checkout_token' => 'd97ed29d-8395-4d1c-bac5-9dca548d760a'])
            ->post(route('checkout.store'), array_merge($this->customer(), ['subtotal_gnf' => 1]))
            ->assertRedirect(route('checkout.success'));

        $this->assertSame(930000, Order::firstOrFail()->subtotal_gnf);
    }

    public function test_checkout_rejects_stale_stock(): void
    {
        $product = $this->item(stock: 1);
        $this->withSession(['cart' => [$product->id => 3], 'checkout_token' => 'd97ed29d-8395-4d1c-bac5-9dca548d760a'])
            ->post(route('checkout.store'), $this->customer())
            ->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_admin_reservation_and_cancel_restore_stock(): void
    {
        $product = $this->item(stock: 5);
        $this->withSession(['cart' => [$product->id => 2], 'checkout_token' => 'd97ed29d-8395-4d1c-bac5-9dca548d760a'])
            ->post(route('checkout.store'), $this->customer());
        $order = Order::firstOrFail();

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin.orders@example.test',
            'password' => 'test-password-12345',
            'is_admin' => true,
        ]);
        $this->actingAs($admin)->patch(route('admin.orders.reserve', $order))->assertRedirect();
        $this->assertSame('reserved', $order->fresh()->status);
        $this->assertSame('unpaid', $order->fresh()->payment_status);
        $this->assertSame(3, $product->fresh()->stock_qty);

        $this->actingAs($admin)->patch(route('admin.orders.cancel', $order))->assertRedirect();
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock_qty);
    }

    public function test_non_admin_cannot_read_private_orders(): void
    {
        $this->get('/admin/commandes')->assertRedirect(route('login'));
        $user = User::create([
            'name' => 'Client',
            'email' => 'client.unauthorized@example.test',
            'password' => 'test-password-12345',
            'is_admin' => false,
        ]);
        $this->actingAs($user)->get('/admin/commandes')->assertForbidden();
    }
}
