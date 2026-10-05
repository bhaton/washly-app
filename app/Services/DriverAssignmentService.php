<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Pickup;
use App\Models\User;
use InvalidArgumentException;

class DriverAssignmentService
{
    public function __construct(
        protected OrderStatusService $orderStatusService
    ) {}

    public function assignPickupDriver(Order $order, User $driver, User $admin): Order
    {
        if (!$driver->hasRole('driver') || !$driver->is_active) {
            throw new InvalidArgumentException('User yang dipilih bukan driver aktif.');
        }

        $order->pickup_driver_id = $driver->id;
        $order->save();

        Pickup::updateOrCreate(
            ['order_id' => $order->id],
            [
                'driver_id' => $driver->id,
                'status' => 'ASSIGNED',
                'scheduled_date' => $order->pickup_date,
                'scheduled_time' => $order->pickup_time,
                'notes' => $order->pickup_notes,
            ]
        );

        $pickupStageStatuses = ['MENUNGGU_PICKUP', 'CONFIRMED', 'WAITING_PICKUP', 'PICKUP_ASSIGNED'];
        if (in_array($order->status, $pickupStageStatuses)) {
            $this->orderStatusService->transition(
                $order,
                'DRIVER_DITUGASKAN',
                $admin,
                "Driver pickup ditugaskan: {$driver->name}"
            );
        } else {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $order->status,
                'to_status' => $order->status,
                'changed_by' => $admin->id,
                'changed_by_role' => 'Admin',
                'notes' => "Driver pickup ditugaskan/diubah: {$driver->name}",
            ]);
        }

        return $order;
    }

    public function assignDeliveryDriver(Order $order, User $driver, User $admin): Order
    {
        if (!$driver->hasRole('driver') || !$driver->is_active) {
            throw new InvalidArgumentException('User yang dipilih bukan driver aktif.');
        }

        $order->delivery_driver_id = $driver->id;
        $order->save();

        Delivery::updateOrCreate(
            ['order_id' => $order->id],
            [
                'driver_id' => $driver->id,
                'status' => 'ASSIGNED',
                'notes' => $order->delivery_notes,
            ]
        );

        $deliveryStageStatuses = ['TAGIHAN_DIBUAT', 'READY_FOR_DELIVERY', 'DELIVERY_ASSIGNED'];
        if (in_array($order->status, $deliveryStageStatuses)) {
            $this->orderStatusService->transition(
                $order,
                'DRIVER_PENGIRIMAN_DITUGASKAN',
                $admin,
                "Driver delivery ditugaskan: {$driver->name}"
            );
        } else {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $order->status,
                'to_status' => $order->status,
                'changed_by' => $admin->id,
                'changed_by_role' => 'Admin',
                'notes' => "Driver delivery ditugaskan/diubah: {$driver->name}",
            ]);
        }

        return $order;
    }
}

