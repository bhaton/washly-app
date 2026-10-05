<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerOrderCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_confirm_order_completed()
    {
        $this->seed();

        $customer = User::role('customer')->first();

        $order = Order::create([
            'order_number' => 'ORD-TEST-1234',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'package_type' => 'ekonomis',
            'speed_type' => 'reguler',
            'status' => 'LAUNDRY_DIKEMBALIKAN',
            'subtotal' => 50000,
            'shipping_fee' => 0,
            'total' => 50000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Mawar No 1',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00 - 12:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Mawar No 1',
        ]);

        $this->actingAs($customer);

        Livewire::test(\App\Livewire\Customer\Tracking::class, ['order' => $order])
            ->call('confirmOrderCompleted')
            ->assertSee('Terima kasih! Pesanan Anda telah dikonfirmasi selesai.');

        $order->refresh();
        $this->assertEquals('ORDER_SELESAI', $order->status);
        $this->assertNotNull($order->completed_at);
    }

    public function test_completed_orders_older_than_24h_are_hidden_from_customer_history()
    {
        $this->seed();

        $customer = User::role('customer')->first();

        // Order 1: Completed 2 hours ago (should be VISIBLE)
        $recentCompletedOrder = Order::create([
            'order_number' => 'ORD-RECENT-2H',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'status' => 'ORDER_SELESAI',
            'completed_at' => now()->subHours(2),
            'subtotal' => 30000,
            'total' => 30000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Mawar',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Mawar',
        ]);

        // Order 2: Completed 30 hours ago (should be HIDDEN)
        $oldCompletedOrder = Order::create([
            'order_number' => 'ORD-OLD-30H',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'status' => 'ORDER_SELESAI',
            'completed_at' => now()->subHours(30),
            'subtotal' => 40000,
            'total' => 40000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Mawar',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Mawar',
        ]);

        $this->actingAs($customer);

        Livewire::test(\App\Livewire\Customer\Orders::class)
            ->assertSee('ORD-RECENT-2H')
            ->assertDontSee('ORD-OLD-30H');
    }

    public function test_customer_can_open_and_print_receipt()
    {
        $this->seed();

        $customer = User::role('customer')->first();

        $order = Order::create([
            'order_number' => 'ORD-RECEIPT-999',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'package_type' => 'ekonomis',
            'speed_type' => 'reguler',
            'status' => 'ORDER_SELESAI',
            'subtotal' => 45000,
            'total' => 45000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Pajajaran No 5',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Pajajaran No 5',
        ]);

        $this->actingAs($customer);

        Livewire::test(\App\Livewire\Customer\OrderDetail::class, ['order' => $order])
            ->call('openReceiptModal')
            ->assertSet('showReceiptModal', true)
            ->assertSee('RESI PEMBAYARAN WASHLY')
            ->assertSee('ORD-RECEIPT-999');

        $order->refresh();
        $this->assertNotNull($order->receipt_printed_at);
    }
}
