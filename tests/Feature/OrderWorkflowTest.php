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

    public function test_full_laundry_end_to_end_workflow()
    {
        Storage::fake('public');

        $customer = User::where('email', 'customer@laundry.test')->first();
        $admin = User::where('email', 'admin@laundry.test')->first();
        $driverPickup = User::where('email', 'driver@laundry.test')->first();
        $driverDelivery = User::where('email', 'driver2@laundry.test')->first();

        $serviceKemeja = Service::where('name', 'Kemeja')->first();
        $serviceCelana = Service::where('name', 'Celana')->first();
        $serviceJaket = Service::where('name', 'Jaket')->first();

        $orderService = app(OrderService::class);
        $paymentService = app(DemoQrisPaymentService::class);
        $assignmentService = app(DriverAssignmentService::class);
        $pickupService = app(PickupService::class);
        $deliveryService = app(DeliveryService::class);
        $statusService = app(OrderStatusService::class);

        // 1. Customer Create Order (Kemeja x2 = 16000, Celana x1 = 10000, Jaket x1 = 15000 => Total 41000)
        $itemsData = [
            ['service_id' => $serviceKemeja->id, 'quantity' => 2],
            ['service_id' => $serviceCelana->id, 'quantity' => 1],
            ['service_id' => $serviceJaket->id, 'quantity' => 1],
        ];

        $pickupData = [
            'pickup_name' => $customer->name,
            'pickup_phone' => $customer->phone,
            'pickup_address' => $customer->address,
            'pickup_date' => now()->format('Y-m-d'),
            'pickup_time' => '10:00',
        ];

        $deliveryData = [
            'delivery_name' => $customer->name,
            'delivery_phone' => $customer->phone,
            'delivery_address' => $customer->address,
        ];

        $order = $orderService->createOrder($customer, $itemsData, $pickupData, $deliveryData);

        $this->assertEquals('PENDING_PAYMENT', $order->status);
        $this->assertEquals(41000.00, $order->total);
        $this->assertEquals(0.00, $order->shipping_fee);

        // 2. Demo QRIS Payment
        $payment = $paymentService->createPayment($order);
        $this->assertEquals('PENDING', $payment->status);
        $this->assertStringStartsWith('DEMO-', $payment->payment_reference);

        $paymentService->processSimulatedPayment($order, $customer);
        $order->refresh();
        $order->load('payment');
        $this->assertEquals('PAID', $order->status);
        $this->assertEquals('PAID', $order->payment->status);

        // 3. Admin Confirmation & Pickup Driver Assignment
        $statusService->transition($order, 'WAITING_CONFIRMATION', $admin);
        $statusService->transition($order, 'CONFIRMED', $admin);
        $statusService->transition($order, 'WAITING_PICKUP', $admin);

        $assignmentService->assignPickupDriver($order, $driverPickup, $admin);
        $order->refresh();
        $this->assertEquals('PICKUP_ASSIGNED', $order->status);
        $this->assertEquals($driverPickup->id, $order->pickup_driver_id);

        // 4. Driver Pickup Action & Proof Upload
        $pickupService->startPickup($order, $driverPickup);
        $order->refresh();
        $this->assertEquals('DRIVER_GOING_TO_PICKUP', $order->status);

        $pickupProofFile = UploadedFile::fake()->image('pickup_proof.jpg');
        $pickupProofPath = $pickupProofFile->store('pickup_proofs', 'public');

        $pickupService->completePickup($order, $driverPickup, $pickupProofPath, 'Pakaian 1 tas plastik rapi');
        $order->refresh();
        $this->assertEquals('PICKED_UP', $order->status);
        $this->assertNotNull($order->pickupProof);

        // 5. Outlet Reception & Processing
        $statusService->transition($order, 'RECEIVED_AT_OUTLET', $admin);
        $this->assertEquals('RECEIVED_AT_OUTLET', $order->status);

        $statusService->transition($order, 'PROCESSING', $admin);
        $this->assertEquals('PROCESSING', $order->status);

        $statusService->transition($order, 'READY_FOR_DELIVERY', $admin);
        $this->assertEquals('READY_FOR_DELIVERY', $order->status);

        // 6. Admin Assign Delivery Driver
        $assignmentService->assignDeliveryDriver($order, $driverDelivery, $admin);
        $order->refresh();
        $this->assertEquals('DELIVERY_ASSIGNED', $order->status);
        $this->assertEquals($driverDelivery->id, $order->delivery_driver_id);

        // 7. Driver Delivery & Proof Upload -> COMPLETED
        $deliveryService->startDelivery($order, $driverDelivery);
        $order->refresh();
        $this->assertEquals('DRIVER_GOING_TO_CUSTOMER', $order->status);

        $deliveryProofFile = UploadedFile::fake()->image('delivery_proof.jpg');
        $deliveryProofPath = $deliveryProofFile->store('delivery_proofs', 'public');

        $deliveryService->completeDelivery($order, $driverDelivery, $deliveryProofPath, 'Diserahkan langsung ke customer');
        $order->refresh();

        // 8. Final Status Verification
        $this->assertEquals('COMPLETED', $order->status);
        $this->assertNotNull($order->deliveryProof);
        $this->assertGreaterThan(5, $order->statusHistories()->count());
    }
}
