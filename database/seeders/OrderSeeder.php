<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Services\OrderService;
use App\Services\DemoQrisPaymentService;
use App\Services\DriverAssignmentService;
use App\Services\OrderStatusService;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'customer@laundry.test')->first();
        $admin = User::where('email', 'admin@laundry.test')->first();
        $driverPickup = User::where('email', 'driver@laundry.test')->first();
        $driverDelivery = User::where('email', 'driver2@laundry.test')->first();

        if (!$customer || !$admin || !$driverPickup || !$driverDelivery) {
            return;
        }

        $orderService = app(OrderService::class);
        $paymentService = app(DemoQrisPaymentService::class);
        $driverService = app(DriverAssignmentService::class);
        $statusService = app(OrderStatusService::class);

        $kemeja = Service::where('name', 'Kemeja')->first();
        $celana = Service::where('name', 'Celana')->first();
        $jaket = Service::where('name', 'Jaket')->first();
        $kaos = Service::where('name', 'Kaos')->first();

        $pickupData = [
            'pickup_name' => $customer->name,
            'pickup_phone' => $customer->phone,
            'pickup_address' => $customer->address,
            'pickup_date' => now()->format('Y-m-d'),
            'pickup_time' => '10:00',
            'pickup_notes' => 'Tolong jemput di lantai 2',
        ];

        $deliveryData = [
            'delivery_name' => $customer->name,
            'delivery_phone' => $customer->phone,
            'delivery_address' => $customer->address,
            'delivery_notes' => 'Titip di pos sekuriti bila tidak ada di rumah',
        ];

        // 1. Order PENDING_PAYMENT
        $order1 = $orderService->createOrder(
            $customer,
            [
                ['service_id' => $kemeja->id, 'quantity' => 2],
                ['service_id' => $celana->id, 'quantity' => 1],
            ],
            $pickupData,
            $deliveryData
        );
        $paymentService->createPayment($order1);

        // 2. Order PAID / WAITING_PICKUP
        $order2 = $orderService->createOrder(
            $customer,
            [
                ['service_id' => $kemeja->id, 'quantity' => 2],
                ['service_id' => $celana->id, 'quantity' => 1],
                ['service_id' => $jaket->id, 'quantity' => 1],
            ],
            $pickupData,
            $deliveryData
        );
        $paymentService->processSimulatedPayment($order2, $customer);
        $statusService->transition($order2, 'WAITING_CONFIRMATION', $admin, 'Admin memeriksa pesanan');
        $statusService->transition($order2, 'CONFIRMED', $admin, 'Pesanan dikonfirmasi oleh outlet');
        $statusService->transition($order2, 'WAITING_PICKUP', $admin, 'Menunggu penugasan driver pickup');

        // 3. Order PICKUP_ASSIGNED
        $order3 = $orderService->createOrder(
            $customer,
            [
                ['service_id' => $kaos->id, 'quantity' => 3],
                ['service_id' => $celana->id, 'quantity' => 2],
            ],
            $pickupData,
            $deliveryData
        );
        $paymentService->processSimulatedPayment($order3, $customer);
        $statusService->transition($order3, 'WAITING_CONFIRMATION', $admin);
        $statusService->transition($order3, 'CONFIRMED', $admin);
        $driverService->assignPickupDriver($order3, $driverPickup, $admin);

        // 4. Order PROCESSING
        $order4 = $orderService->createOrder(
            $customer,
            [
                ['service_id' => $jaket->id, 'quantity' => 2],
            ],
            $pickupData,
            $deliveryData
        );
        $paymentService->processSimulatedPayment($order4, $customer);
        $statusService->transition($order4, 'WAITING_CONFIRMATION', $admin);
        $statusService->transition($order4, 'CONFIRMED', $admin);
        $driverService->assignPickupDriver($order4, $driverPickup, $admin);
        $statusService->transition($order4, 'DRIVER_GOING_TO_PICKUP', $driverPickup);
        $statusService->transition($order4, 'PICKED_UP', $driverPickup);
        $statusService->transition($order4, 'RECEIVED_AT_OUTLET', $admin);
        $statusService->transition($order4, 'PROCESSING', $admin, 'Laundry sedang dicuci & disetrika');
    }
}
