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

        if ($order->status !== 'PICKUP_ASSIGNED') {
            throw new InvalidArgumentException("Status order tidak valid untuk memulai pickup: {$order->status}");
        }

        $pickup = Pickup::where('order_id', $order->id)->first();
        if ($pickup) {
            $pickup->update(['status' => 'IN_PROGRESS']);
        }

        return $this->orderStatusService->transition(
            $order,
            'DRIVER_GOING_TO_PICKUP',
            $driver,
            'Driver dalam perjalanan menuju lokasi customer'
        );
    }

    public function completePickup(Order $order, User $driver, string $imagePath, ?string $notes = null): Order
    {
        if ($order->pickup_driver_id !== $driver->id) {
            throw new InvalidArgumentException('Anda tidak berhak mengakses tugas pickup ini.');
        }

        if (!in_array($order->status, ['DRIVER_GOING_TO_PICKUP', 'PICKUP_ASSIGNED'])) {
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
        }

        PickupProof::create([
            'pickup_id' => $pickup?->id,
            'order_id' => $order->id,
            'driver_id' => $driver->id,
            'image_path' => $imagePath,
            'notes' => $notes,
        ]);

        return $this->orderStatusService->transition(
            $order,
            'PICKED_UP',
            $driver,
            'Laundry telah berhasil dijemput oleh driver'
        );
    }
}
