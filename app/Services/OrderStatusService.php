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
        // Main Workflow
        'MENUNGGU_PICKUP' => ['DRIVER_DITUGASKAN', 'LAUNDRY_DIJEMPUT', 'LAUNDRY_DITERIMA', 'CANCELLED'],
        'DRIVER_DITUGASKAN' => ['LAUNDRY_DIJEMPUT', 'LAUNDRY_DITERIMA', 'PROSES_LAUNDRY', 'DRIVER_GOING_TO_PICKUP', 'CANCELLED'],

        'LAUNDRY_DIJEMPUT' => ['LAUNDRY_DITERIMA', 'PROSES_LAUNDRY', 'CANCELLED'],
        'LAUNDRY_DITERIMA' => ['PROSES_LAUNDRY', 'LAUNDRY_SELESAI', 'CANCELLED'],
        'PROSES_LAUNDRY' => ['LAUNDRY_SELESAI', 'PENIMBANGAN', 'TAGIHAN_DIBUAT', 'CANCELLED'],
        'LAUNDRY_SELESAI' => ['PENIMBANGAN', 'TAGIHAN_DIBUAT', 'CANCELLED'],
        'PENIMBANGAN' => ['TAGIHAN_DIBUAT', 'CANCELLED'],
        'TAGIHAN_DIBUAT' => ['DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN', 'CANCELLED'],
        'DRIVER_PENGIRIMAN_DITUGASKAN' => ['LAUNDRY_DIKEMBALIKAN', 'PEMBAYARAN_DRIVER', 'ORDER_SELESAI', 'CANCELLED'],
        'LAUNDRY_DIKEMBALIKAN' => ['PEMBAYARAN_DRIVER', 'ORDER_SELESAI', 'CANCELLED'],
        'PEMBAYARAN_DRIVER' => ['ORDER_SELESAI', 'CANCELLED'],
        'ORDER_SELESAI' => [],

        // Legacy compatibility
        'PENDING_PAYMENT' => ['MENUNGGU_PICKUP', 'PAID', 'CANCELLED'],
        'PAID' => ['MENUNGGU_PICKUP', 'WAITING_CONFIRMATION', 'CONFIRMED', 'CANCELLED'],
        'WAITING_CONFIRMATION' => ['MENUNGGU_PICKUP', 'CONFIRMED', 'CANCELLED'],
        'CONFIRMED' => ['MENUNGGU_PICKUP', 'WAITING_PICKUP', 'PICKUP_ASSIGNED', 'DRIVER_DITUGASKAN', 'CANCELLED'],
        'WAITING_PICKUP' => ['DRIVER_DITUGASKAN', 'PICKUP_ASSIGNED', 'CANCELLED'],
        'PICKUP_ASSIGNED' => ['DRIVER_GOING_TO_PICKUP', 'LAUNDRY_DIJEMPUT', 'CANCELLED'],
        'DRIVER_GOING_TO_PICKUP' => ['PICKED_UP', 'LAUNDRY_DIJEMPUT', 'CANCELLED'],
        'PICKED_UP' => ['RECEIVED_AT_OUTLET', 'LAUNDRY_DITERIMA', 'CANCELLED'],
        'RECEIVED_AT_OUTLET' => ['PROSES_LAUNDRY', 'PROCESSING', 'CANCELLED'],
        'PROCESSING' => ['LAUNDRY_SELESAI', 'READY_FOR_DELIVERY', 'CANCELLED'],
        'READY_FOR_DELIVERY' => ['TAGIHAN_DIBUAT', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'DELIVERY_ASSIGNED', 'CANCELLED'],
        'DELIVERY_ASSIGNED' => ['DRIVER_GOING_TO_CUSTOMER', 'LAUNDRY_DIKEMBALIKAN', 'CANCELLED'],
        'DRIVER_GOING_TO_CUSTOMER' => ['DELIVERED', 'LAUNDRY_DIKEMBALIKAN', 'CANCELLED'],
        'DELIVERED' => ['PEMBAYARAN_DRIVER', 'ORDER_SELESAI', 'COMPLETED'],
        'COMPLETED' => [],
        'CANCELLED' => [],
    ];

    public static array $statusLabels = [
        'MENUNGGU_PICKUP' => '1. Menunggu Pickup',
        'DRIVER_DITUGASKAN' => '2. Driver Pickup Ditugaskan',
        'LAUNDRY_DIJEMPUT' => '3. Laundry Dijemput Driver',
        'LAUNDRY_DITERIMA' => '4. Laundry Diterima di Outlet',
        'PROSES_LAUNDRY' => '5. Proses Pencucian / Laundry',
        'LAUNDRY_SELESAI' => '6. Laundry Selesai Diproses',
        'PENIMBANGAN' => '7. Penimbangan Berat Akhir',
        'TAGIHAN_DIBUAT' => '8. Tagihan Dibuat',
        'DRIVER_PENGIRIMAN_DITUGASKAN' => '9. Driver Delivery Ditugaskan',
        'LAUNDRY_DIKEMBALIKAN' => '10. Laundry Dikembalikan / Diterima Customer',
        'PEMBAYARAN_DRIVER' => '11. Pembayaran Driver Dikonfirmasi',
        'ORDER_SELESAI' => '12. Order Selesai (Completed)',
        'CANCELLED' => 'X. Dibatalkan (Cancelled)',
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

        if (!$this->canTransition($fromStatus, $toStatus, $user)) {
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

        if (in_array($toStatus, ['ORDER_SELESAI', 'COMPLETED'], true) && !$order->completed_at) {
            $order->completed_at = now();
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

    public function canTransition(string $fromStatus, string $toStatus, ?User $user = null): bool
    {
        if ($user && $user->hasRole('admin')) {
            return true;
        }

        $allowed = $this->allowedTransitions[$fromStatus] ?? [];
        return in_array($toStatus, $allowed, true);
    }
}

