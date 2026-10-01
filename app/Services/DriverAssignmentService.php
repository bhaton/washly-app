<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Order;
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

        if (!in_array($order->status, ['CONFIRMED', 'WAITING_PICKUP', 'PICKUP_ASSIGNED'])) {
            throw new InvalidArgumentException("Order dengan status '{$order->status}' tidak dapat ditugaskan untuk pickup.");
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

        if ($order->status !== 'PICKUP_ASSIGNED') {
            $this->orderStatusService->transition(
                $order,
                'PICKUP_ASSIGNED',
                $admin,
                "Driver pickup ditugaskan: {$driver->name}"
            );
        }

        return $order;
    }

    public function assignDeliveryDriver(Order $order, User $driver, User $admin): Order
    {
        if (!$driver->hasRole('driver') || !$driver->is_active) {
            throw new InvalidArgumentException('User yang dipilih bukan driver aktif.');
        }

        if (!in_array($order->status, ['READY_FOR_DELIVERY', 'DELIVERY_ASSIGNED'])) {
            throw new InvalidArgumentException("Order dengan status '{$order->status}' tidak dapat ditugaskan untuk delivery.");
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

        if ($order->status !== 'DELIVERY_ASSIGNED') {
            $this->orderStatusService->transition(
                $order,
                'DELIVERY_ASSIGNED',
                $admin,
                "Driver delivery ditugaskan: {$driver->name}"
            );
        }

        return $order;
    }
}
