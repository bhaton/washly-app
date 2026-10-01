<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Database\Seeders\RoleAndUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class StateTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndUserSeeder::class);
    }

    public function test_invalid_arbitrary_status_jump_is_rejected()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();

        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'customer_id' => $customer->id,
            'status' => 'PENDING_PAYMENT',
            'subtotal' => 10000,
            'shipping_fee' => 0,
            'total' => 10000,
            'pickup_name' => $customer->name,
            'pickup_phone' => $customer->phone,
            'pickup_address' => $customer->address,
            'pickup_date' => now()->format('Y-m-d'),
            'pickup_time' => '10:00',
            'delivery_name' => $customer->name,
            'delivery_phone' => $customer->phone,
            'delivery_address' => $customer->address,
        ]);

        $statusService = app(OrderStatusService::class);

        // Attempt invalid jump directly from PENDING_PAYMENT to COMPLETED
        $this->expectException(InvalidArgumentException::class);
        $statusService->transition($order, 'COMPLETED', $customer);
    }
}
