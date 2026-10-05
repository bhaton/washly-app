<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Services\DeliveryService;
use App\Services\DemoQrisPaymentService;
use App\Services\DriverAssignmentService;
use App\Services\OrderService;
use App\Services\OrderStatusService;
use App\Services\PickupService;
use Database\Seeders\RoleAndUserSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndUserSeeder::class);
        $this->seed(ServiceSeeder::class);
    }

    public function test_new_laundry_workflow_end_to_end()
    {
        Storage::fake('public');

        $customer = User::where('email', 'customer@laundry.test')->first();
        $admin = User::where('email', 'admin@laundry.test')->first();
        $driverPickup = User::where('email', 'driver@laundry.test')->first();
        $driverDelivery = User::where('email', 'driver2@laundry.test')->first();

        $serviceKiloan = Service::where('category', 'kiloan')->where('package_type', 'ekonomis')->first();
        $serviceKaos = Service::where('name', 'Kaos')->first();

        $orderService = app(OrderService::class);
        $assignmentService = app(DriverAssignmentService::class);
        $pickupService = app(PickupService::class);
        $deliveryService = app(DeliveryService::class);
        $statusService = app(OrderStatusService::class);

        // 1. Customer Create Order
        $itemsData = [
            ['service_id' => $serviceKaos->id, 'quantity' => 10],
        ];

        $pickupData = [
            'pickup_name' => $customer->name,
            'pickup_phone' => $customer->phone ?? '081234567890',
            'pickup_address' => $customer->address ?? 'Jl. Merdeka No. 1',
            'pickup_date' => now()->format('Y-m-d'),
            'pickup_time' => '10:00',
        ];

        $deliveryData = [
            'delivery_name' => $customer->name,
            'delivery_phone' => $customer->phone ?? '081234567890',
            'delivery_address' => $customer->address ?? 'Jl. Merdeka No. 1',
        ];

        $extraOptions = [
            'service_type' => 'kiloan',
            'package_type' => 'ekonomis',
            'speed_type' => 'reguler',
            'estimated_weight' => 2.0,
            'estimated_price' => 16000.00,
        ];

        $order = $orderService->createOrder($customer, $itemsData, $pickupData, $deliveryData, $extraOptions);

        $this->assertEquals('MENUNGGU_PICKUP', $order->status);

        // 2. Admin assign pickup driver
        $assignmentService->assignPickupDriver($order, $driverPickup, $admin);
        $order->refresh();
        $this->assertEquals('DRIVER_DITUGASKAN', $order->status);

        // 3. Driver pickup
        $pickupProofFile = UploadedFile::fake()->image('pickup_proof.jpg');
        $pickupProofPath = $pickupProofFile->store('pickup_proofs', 'public');
        $pickupService->completePickup($order, $driverPickup, $pickupProofPath, 'Diambil 1 kantong');
        $order->refresh();
        $this->assertEquals('LAUNDRY_DITERIMA', $order->status);
        $this->assertNotNull($order->pickupProof);
        $this->assertEquals('Diambil 1 kantong', $order->pickupProof->notes);

        // 4. Admin confirm outlet reception
        $statusService->transition($order, 'PROSES_LAUNDRY', $admin);
        $order->refresh();
        $this->assertEquals('PROSES_LAUNDRY', $order->status);

        // 5. Laundry done & Penimbangan & Tagihan dibuat
        $statusService->transition($order, 'LAUNDRY_SELESAI', $admin);
        $order->actual_weight = 2.5;
        $order->subtotal = 20000.00;
        $order->total = 20000.00;
        $order->save();
        $statusService->transition($order, 'PENIMBANGAN', $admin);
        $statusService->transition($order, 'TAGIHAN_DIBUAT', $admin);
        $order->refresh();
        $this->assertEquals('TAGIHAN_DIBUAT', $order->status);
        $this->assertEquals(20000.00, $order->total);

        // 6. Admin assign delivery driver
        $assignmentService->assignDeliveryDriver($order, $driverDelivery, $admin);
        $order->refresh();
        $this->assertEquals('DRIVER_PENGIRIMAN_DITUGASKAN', $order->status);

        // 7. Driver delivery
        $deliveryProofFile = UploadedFile::fake()->image('delivery_proof.jpg');
        $deliveryProofPath = $deliveryProofFile->store('delivery_proofs', 'public');
        $deliveryService->completeDelivery($order, $driverDelivery, $deliveryProofPath, 'Diserahkan ke customer + resi');
        $order->refresh();
        $this->assertEquals('LAUNDRY_DIKEMBALIKAN', $order->status);
        $this->assertNotNull($order->deliveryProof);
        $this->assertEquals('Diserahkan ke customer + resi', $order->deliveryProof->notes);

        // Verify Customer tracking & order detail livewire components render the driver proofs
        $this->actingAs($customer);
        \Livewire\Livewire::test(\App\Livewire\Customer\OrderDetail::class, ['order' => $order])
            ->assertSee('Diambil 1 kantong')
            ->assertSee('Diserahkan ke customer + resi');

        \Livewire\Livewire::test(\App\Livewire\Customer\Tracking::class, ['order' => $order])
            ->assertSee('Diambil 1 kantong')
            ->assertSee('Diserahkan ke customer + resi');

        // 8. Driver payment confirmation -> ORDER_SELESAI
        $deliveryService->confirmPaymentReceived($order, $driverDelivery);
        $order->refresh();
        $this->assertEquals('ORDER_SELESAI', $order->status);
    }
}
