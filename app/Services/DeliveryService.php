<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\DeliveryProof;
use App\Models\Order;
use App\Models\User;
use InvalidArgumentException;

class DeliveryService
{
    public function __construct(
        protected OrderStatusService $orderStatusService
    ) {}

    public function startDelivery(Order $order, User $driver): Order
    {
        if ($order->delivery_driver_id !== $driver->id) {
            throw new InvalidArgumentException('Anda tidak berhak mengakses tugas delivery ini.');
        }

        $allowed = ['DRIVER_PENGIRIMAN_DITUGASKAN', 'TAGIHAN_DIBUAT', 'DELIVERY_ASSIGNED'];
        if (!in_array($order->status, $allowed)) {
            throw new InvalidArgumentException("Status order tidak valid untuk memulai delivery: {$order->status}");
        }

        $delivery = Delivery::where('order_id', $order->id)->first();
        if ($delivery) {
            $delivery->update(['status' => 'IN_PROGRESS']);
        }

        $targetStatus = $order->status === 'DELIVERY_ASSIGNED' ? 'DRIVER_GOING_TO_CUSTOMER' : 'LAUNDRY_DIKEMBALIKAN';

        return $this->orderStatusService->transition(
            $order,
            $targetStatus,
            $driver,
            'Driver dalam perjalanan mengantar laundry ke lokasi customer'
        );
    }

    public function completeDelivery(Order $order, User $driver, string $imagePath, ?string $notes = null): Order
    {
        if ($order->delivery_driver_id !== $driver->id) {
            throw new InvalidArgumentException('Anda tidak berhak mengakses tugas delivery ini.');
        }

        $allowed = ['DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN', 'DRIVER_GOING_TO_CUSTOMER', 'DELIVERY_ASSIGNED'];
        if (!in_array($order->status, $allowed)) {
            throw new InvalidArgumentException("Status order tidak valid untuk menyelesaikan delivery: {$order->status}");
        }

        if (empty($imagePath)) {
            throw new InvalidArgumentException('Bukti foto delivery wajib diunggah.');
        }

        $delivery = Delivery::where('order_id', $order->id)->first();
        if ($delivery) {
            $delivery->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
            ]);
        }

        DeliveryProof::create([
            'delivery_id' => $delivery?->id,
            'order_id' => $order->id,
            'driver_id' => $driver->id,
            'image_path' => $imagePath,
            'notes' => $notes,
        ]);

        if (in_array($order->status, ['DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN'])) {
            return $this->orderStatusService->transition(
                $order,
                'LAUNDRY_DIKEMBALIKAN',
                $driver,
                'Laundry telah diserahkan kepada customer beserta resi pembayaran'
            );
        }

        $order = $this->orderStatusService->transition(
            $order,
            'DELIVERED',
            $driver,
            'Laundry telah diserahkan kepada customer'
        );

        return $this->orderStatusService->transition(
            $order,
            'COMPLETED',
            $driver,
            'Order pesanan laundry selesai'
        );
    }

    public function confirmPaymentReceived(Order $order, User $driver): Order
    {
        if ($order->delivery_driver_id !== $driver->id) {
            throw new InvalidArgumentException('Anda tidak berhak mengonfirmasi pembayaran tugas ini.');
        }

        if ($order->status !== 'LAUNDRY_DIKEMBALIKAN') {
            throw new InvalidArgumentException("Status order harus 'LAUNDRY_DIKEMBALIKAN' untuk konfirmasi pembayaran.");
        }

        $order = $this->orderStatusService->transition(
            $order,
            'PEMBAYARAN_DRIVER',
            $driver,
            'Driver menerima pembayaran dari customer'
        );

        return $this->orderStatusService->transition(
            $order,
            'ORDER_SELESAI',
            $driver,
            'Order pesanan laundry selesai sepenuhnya'
        );
    }
}
