<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerPaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_process_qris_gateway_payment()
    {
        $this->seed();

        $customer = User::role('customer')->first();

        $order = Order::create([
            'order_number' => 'ORD-PAY-QRIS-001',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'package_type' => 'ekonomis',
            'speed_type' => 'reguler',
            'status' => 'TAGIHAN_DIBUAT',
            'actual_weight' => 5.0,
            'subtotal' => 50000,
            'shipping_fee' => 0,
            'total' => 50000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Bogor No 123',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Bogor No 123',
        ]);

        $this->actingAs($customer);

        Livewire::test(\App\Livewire\Customer\OrderDetail::class, ['order' => $order])
            ->call('openPaymentModal')
            ->assertSet('showPaymentModal', true)
            ->call('selectPaymentMethod', 'QRIS')
            ->call('processGatewayPayment')
            ->assertSee('Pembayaran via Gateway (QRIS) berhasil dikonfirmasi');

        $order->refresh();
        $this->assertNotNull($order->payment);
        $this->assertEquals('PAID', $order->payment->status);
        $this->assertEquals('QRIS', $order->payment->payment_method);
        $this->assertStringStartsWith('PAY-QRIS-', $order->payment->payment_reference);
    }

    public function test_customer_can_process_va_bca_gateway_payment()
    {
        $this->seed();

        $customer = User::role('customer')->first();

        $order = Order::create([
            'order_number' => 'ORD-PAY-VABCA-002',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'package_type' => 'premium',
            'speed_type' => 'ekspres',
            'status' => 'TAGIHAN_DIBUAT',
            'actual_weight' => 5.0,
            'subtotal' => 75000,
            'shipping_fee' => 0,
            'total' => 75000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Pajajaran 45',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '09:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Pajajaran 45',
        ]);

        $this->actingAs($customer);

        Livewire::test(\App\Livewire\Customer\Tracking::class, ['order' => $order])
            ->call('openPaymentModal')
            ->assertSet('showPaymentModal', true)
            ->call('selectPaymentMethod', 'VA_BCA')
            ->call('processGatewayPayment')
            ->assertSee('Pembayaran via Gateway (VA_BCA) berhasil dikonfirmasi');

        $order->refresh();
        $this->assertNotNull($order->payment);
        $this->assertEquals('PAID', $order->payment->status);
        $this->assertEquals('VA_BCA', $order->payment->payment_method);
        $this->assertStringStartsWith('PAY-BCA-', $order->payment->payment_reference);
    }
}
