<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_midtrans_service_creates_snap_token_and_payment_record()
    {
        $this->seed();

        $customer = User::role('customer')->first();

        $order = Order::create([
            'order_number' => 'ORD-MIDTRANS-100',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'package_type' => 'ekonomis',
            'speed_type' => 'reguler',
            'status' => 'PENDING_PAYMENT',
            'subtotal' => 60000,
            'shipping_fee' => 0,
            'total' => 60000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Pajajaran Bogor',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Pajajaran Bogor',
        ]);

        $midtransService = app(MidtransService::class);
        $result = $midtransService->createSnapToken($order);

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['snap_token']);
        $this->assertNotEmpty($result['payment_reference']);

        $order->refresh();
        $this->assertNotNull($order->payment);
        $this->assertEquals('PENDING', $order->payment->status);
    }

    public function test_midtrans_webhook_endpoint_processes_settlement_notification()
    {
        $this->seed();

        $customer = User::role('customer')->first();

        $order = Order::create([
            'order_number' => 'ORD-WEBHOOK-200',
            'customer_id' => $customer->id,
            'service_type' => 'kiloan',
            'package_type' => 'premium',
            'speed_type' => 'ekspres',
            'status' => 'PENDING_PAYMENT',
            'subtotal' => 85000,
            'shipping_fee' => 0,
            'total' => 85000,
            'pickup_name' => $customer->name,
            'pickup_phone' => '08123456789',
            'pickup_address' => 'Jl Pajajaran Bogor',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => '08123456789',
            'delivery_address' => 'Jl Pajajaran Bogor',
        ]);

        $paymentRef = 'MID-20261004-' . $order->id . '-TEST';

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'Midtrans Gateway',
            'payment_reference' => $paymentRef,
            'amount' => 85000,
            'status' => 'PENDING',
        ]);

        $payload = [
            'order_id' => $paymentRef,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'gross_amount' => 85000,
            'status_code' => '200',
        ];

        $response = $this->postJson('/api/midtrans/notification', $payload);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $order->refresh();
        $payment->refresh();

        $this->assertEquals('PAID', $payment->status);
        $this->assertEquals('PAID', $order->status);
        $this->assertNotNull($payment->paid_at);
    }
}
