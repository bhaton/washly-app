<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminOrderManagementFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_order()
    {
        $this->seed();

        $admin = User::role('admin')->first();
        $customer = User::role('customer')->first();
        $this->actingAs($admin);

        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'status' => 'MENUNGGU_PICKUP',
            'subtotal' => 25000,
            'total' => 25000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Melati',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Melati',
        ]);
        $orderNum = $order->order_number;

        Livewire::test(\App\Livewire\Admin\Orders\Index::class)
            ->call('confirmDelete', $order->id)
            ->call('deleteOrder')
            ->assertSee("Order {$orderNum} berhasil dihapus.");

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_admin_can_filter_orders_by_date_period()
    {
        $this->seed();

        $admin = User::role('admin')->first();
        $customer = User::role('customer')->first();
        $this->actingAs($admin);

        // Order today
        $todayOrder = Order::create([
            'order_number' => 'ORD-TODAY-001',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'status' => 'MENUNGGU_PICKUP',
            'subtotal' => 25000,
            'total' => 25000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Melati',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Melati',
            'created_at' => now(),
        ]);

        // Order last month
        $pastOrder = Order::create([
            'order_number' => 'ORD-PAST-002',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'status' => 'MENUNGGU_PICKUP',
            'subtotal' => 30000,
            'total' => 30000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Melati',
            'pickup_date' => now()->subMonths(2)->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Melati',
        ]);

        \Illuminate\Support\Facades\DB::table('orders')
            ->where('id', $pastOrder->id)
            ->update(['created_at' => now()->subMonths(2)]);

        // Test Today filter
        Livewire::test(\App\Livewire\Admin\Orders\Index::class)
            ->set('dateFilter', 'today')
            ->assertSee('ORD-TODAY-001')
            ->assertDontSee('ORD-PAST-002');
    }

    public function test_wa_direct_link_generation()
    {
        $this->seed();

        $admin = User::role('admin')->first();
        $customer = User::role('customer')->first();
        $this->actingAs($admin);

        $order = Order::create([
            'order_number' => 'ORD-TEST-002',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'status' => 'MENUNGGU_PICKUP',
            'subtotal' => 25000,
            'total' => 25000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Melati',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Melati',
        ]);

        Livewire::test(\App\Livewire\Admin\Orders\Index::class)
            ->assertSee('https://wa.me/62');
    }

    public function test_admin_can_view_order_detail()
    {
        $this->seed();

        $admin = User::role('admin')->first();
        $customer = User::role('customer')->first();
        $this->actingAs($admin);

        $order = Order::create([
            'order_number' => 'ORD-TEST-003',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'status' => 'MENUNGGU_PICKUP',
            'subtotal' => 25000,
            'total' => 25000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Melati',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Melati',
        ]);

        Livewire::test(\App\Livewire\Admin\Orders\Show::class, ['order' => $order])
            ->assertSee($order->order_number);
    }

    public function test_admin_can_view_and_verify_payment_status_and_proof()
    {
        $this->seed();

        $admin = User::role('admin')->first();
        $customer = User::role('customer')->first();
        $this->actingAs($admin);

        $order = Order::create([
            'order_number' => 'ORD-PAY-PROOF-001',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'status' => 'TAGIHAN_DIBUAT',
            'subtotal' => 45000,
            'total' => 45000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Melati',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Melati',
        ]);

        \App\Models\Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'Midtrans (QRIS Sandbox)',
            'payment_reference' => 'MIDTRANS-REF-12345',
            'amount' => 45000,
            'status' => 'PENDING',
        ]);

        Livewire::test(\App\Livewire\Admin\Orders\Show::class, ['order' => $order])
            ->assertSee('BELUM BAYAR')
            ->call('openPaymentProofModal')
            ->assertSet('showPaymentProofModal', true)
            ->assertSee('MIDTRANS-REF-12345')
            ->call('confirmPaymentAsPaid')
            ->assertSee('LUNAS');

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'PAID',
        ]);
    }

    public function test_admin_can_export_executive_pdf_report()
    {
        $this->seed();

        $admin = User::role('admin')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.reports.pdf', ['period' => 'month']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
