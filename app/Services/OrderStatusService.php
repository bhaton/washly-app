<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use InvalidArgumentException;

class OrderStatusService
{
    /**
     * Allowed transition table: from_status => array of allowed to_status
     */
    protected array $allowedTransitions = [
        'PENDING_PAYMENT' => ['PAID', 'CANCELLED'],
        'PAID' => ['WAITING_CONFIRMATION', 'CONFIRMED', 'CANCELLED'],
        'WAITING_CONFIRMATION' => ['CONFIRMED', 'CANCELLED'],
        'CONFIRMED' => ['WAITING_PICKUP', 'PICKUP_ASSIGNED', 'CANCELLED'],
        'WAITING_PICKUP' => ['PICKUP_ASSIGNED', 'CANCELLED'],
        'PICKUP_ASSIGNED' => ['DRIVER_GOING_TO_PICKUP', 'CANCELLED'],
        'DRIVER_GOING_TO_PICKUP' => ['PICKED_UP', 'CANCELLED'],
        'PICKED_UP' => ['RECEIVED_AT_OUTLET', 'CANCELLED'],
        'RECEIVED_AT_OUTLET' => ['PROCESSING', 'CANCELLED'],
        'PROCESSING' => ['READY_FOR_DELIVERY', 'CANCELLED'],
        'READY_FOR_DELIVERY' => ['DELIVERY_ASSIGNED', 'CANCELLED'],
        'DELIVERY_ASSIGNED' => ['DRIVER_GOING_TO_CUSTOMER', 'CANCELLED'],
        'DRIVER_GOING_TO_CUSTOMER' => ['DELIVERED', 'CANCELLED'],
        'DELIVERED' => ['COMPLETED'],
        'COMPLETED' => [],
        'CANCELLED' => [],
    ];

    /**
     * Change order status with validation and history logging.
     */
    public function transition(Order $order, string $toStatus, ?User $user = null, ?string $notes = null): Order
    {
        $fromStatus = $order->status;

        if ($fromStatus === $toStatus) {
            return $order;
        }

        if (!$this->canTransition($fromStatus, $toStatus)) {
            throw new InvalidArgumentException("Perubahan status dari '{$fromStatus}' ke '{$toStatus}' tidak diperbolehkan.");
        }

        $userRole = null;
        if ($user) {
            if ($user->hasRole('admin')) {
                $userRole = 'Admin';
            } elseif ($user->hasRole('driver')) {
                $userRole = 'Driver';
            } elseif ($user->hasRole('customer')) {
                $userRole = 'Customer';
            }
        }

        $order->status = $toStatus;
        $order->save();

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by' => $user ? $user->id : null,
            'changed_by_role' => $userRole ?? 'System',
            'notes' => $notes,
        ]);

        return $order;
    }

    public function canTransition(string $fromStatus, string $toStatus): bool
    {
        $allowed = $this->allowedTransitions[$fromStatus] ?? [];
        return in_array($toStatus, $allowed, true);
    }
}
