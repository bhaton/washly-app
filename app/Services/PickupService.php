<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Pickup;
use App\Models\PickupProof;
use App\Models\User;
use InvalidArgumentException;

class PickupService
{
    public function __construct(
        protected OrderStatusService $orderStatusService
    ) {}

    public function startPickup(Order $order, User $driver): Order
    {
        if ($order->pickup_driver_id !== $driver->id) {
            throw new InvalidArgumentException('Anda tidak berhak mengakses tugas pickup ini.');
        }

        $allowed = ['DRIVER_DITUGASKAN', 'PICKUP_ASSIGNED'];
        if (!in_array($order->status, $allowed)) {
            throw new InvalidArgumentException("Status order tidak valid untuk memulai pickup: {$order->status}");
        }

        $pickup = Pickup::where('order_id', $order->id)->first();
        if ($pickup) {
            $pickup->update(['status' => 'IN_PROGRESS']);
        }

        $targetStatus = $order->status === 'DRIVER_DITUGASKAN' ? 'LAUNDRY_DIJEMPUT' : 'DRIVER_GOING_TO_PICKUP';

        return $this->orderStatusService->transition(
            $order,
            $targetStatus,
            $driver,
            'Driver dalam perjalanan / menjemput laundry customer'
        );
    }

    public function completePickup(Order $order, User $driver, string $imagePath, ?string $notes = null): Order
    {
        if ($order->pickup_driver_id !== $driver->id) {
            throw new InvalidArgumentException('Anda tidak berhak mengakses tugas pickup ini.');
        }

        $allowed = ['DRIVER_DITUGASKAN', 'LAUNDRY_DIJEMPUT', 'DRIVER_GOING_TO_PICKUP', 'PICKUP_ASSIGNED'];
        if (!in_array($order->status, $allowed)) {
            throw new InvalidArgumentException("Status order tidak valid untuk menyelesaikan pickup: {$order->status}");
        }

        if (empty($imagePath)) {
            throw new InvalidArgumentException('Bukti foto pickup wajib diunggah.');
        }

        $pickup = Pickup::where('order_id', $order->id)->first();
        if ($pickup) {
            $pickup->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
            ]);
        } else {
            Pickup::create([
                'order_id' => $order->id,
                'driver_id' => $driver->id,
                'status' => 'COMPLETED',
                'completed_at' => now(),
                'scheduled_date' => $order->pickup_date,
                'scheduled_time' => $order->pickup_time,
                'notes' => $order->pickup_notes,
            ]);
        }

        PickupProof::create([
            'pickup_id' => $pickup?->id,
            'order_id' => $order->id,
            'driver_id' => $driver->id,
            'image_path' => $imagePath,
            'notes' => $notes,
        ]);

        $targetStatus = in_array($order->status, ['DRIVER_DITUGASKAN', 'LAUNDRY_DIJEMPUT']) ? 'LAUNDRY_DITERIMA' : 'PICKED_UP';

        return $this->orderStatusService->transition(
            $order,
            $targetStatus,
            $driver,
            'Laundry telah berhasil dijemput oleh driver dan diterima di outlet'
        );
    }
}
