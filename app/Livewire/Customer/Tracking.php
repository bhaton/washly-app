<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Tracking extends Component
{
    public Order $order;
    public bool $showReceiptModal = false;
    public bool $showPaymentModal = false;
    public string $selectedPaymentMethod = 'QRIS';

    public function openReceiptModal()
    {
        $this->showReceiptModal = true;
        if (!$this->order->receipt_printed_at) {
            $this->order->receipt_printed_at = now();
            $this->order->save();
        }
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
    }

    public function openPaymentModal()
    {
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
    }

    public function selectPaymentMethod(string $method)
    {
        $this->selectedPaymentMethod = $method;
    }

    public function processGatewayPayment(\App\Services\DemoQrisPaymentService $paymentService)
    {
        $isPaymentAllowed = in_array($this->order->status, ['LAUNDRY_SELESAI', 'PENIMBANGAN', 'TAGIHAN_DIBUAT', 'READY_FOR_DELIVERY', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN']);
        if (!$isPaymentAllowed) {
            session()->flash('error', 'Pembayaran hanya dapat dilakukan setelah proses laundry selesai ditimbang dan sebelum tahap pengiriman driver.');
            return;
        }

        try {
            $paymentService->processSimulatedPayment($this->order, Auth::user(), $this->selectedPaymentMethod);
            $this->order->refresh();
            session()->flash('message', '✓ Pembayaran via Gateway (' . $this->selectedPaymentMethod . ') berhasil dikonfirmasi!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function payWithMidtrans(\App\Services\MidtransService $midtransService)
    {
        $isPaymentAllowed = in_array($this->order->status, ['LAUNDRY_SELESAI', 'PENIMBANGAN', 'TAGIHAN_DIBUAT', 'READY_FOR_DELIVERY', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN']);
        if (!$isPaymentAllowed) {
            session()->flash('error', 'Pembayaran hanya dapat dilakukan setelah proses laundry selesai ditimbang dan sebelum tahap pengiriman driver.');
            return;
        }

        try {
            $result = $midtransService->createSnapToken($this->order);
            if (!empty($result['snap_token'])) {
                $this->dispatch('trigger-midtrans-pay', token: $result['snap_token'], orderNumber: $this->order->order_number);
                session()->flash('message', 'Membuka Midtrans Snap Payment Gateway...');
            }
        } catch (\Throwable $e) {
            session()->flash('error', 'Midtrans Gateway Error: ' . $e->getMessage());
        }
    }

    public function mount(Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tracking pesanan ini.');
        }

        $this->order = $order->load([
            'payment',
            'pickupDriver',
            'deliveryDriver',
            'statusHistories.changedBy',
            'pickupProof',
            'deliveryProof'
        ]);
    }

    public function render()
    {
        // Define timeline stages mapped to all potential admin order status codes & legacy aliases
        $timelineStages = [
            [
                'key' => 'MENUNGGU_PICKUP',
                'aliases' => ['MENUNGGU_PICKUP', 'PENDING_PAYMENT', 'PAID', 'WAITING_PICKUP'],
                'label' => 'Order Dibuat & Menunggu Pickup',
                'desc' => 'Pesanan telah diterima dan menunggu penugasan driver pickup.',
            ],
            [
                'key' => 'DRIVER_DITUGASKAN',
                'aliases' => ['DRIVER_DITUGASKAN', 'PICKUP_ASSIGNED', 'CONFIRMED', 'WAITING_CONFIRMATION'],
                'label' => 'Driver Pickup Ditugaskan',
                'desc' => 'Driver pickup telah ditugaskan dan sedang bersiap / menuju lokasi penjemputan.',
            ],
            [
                'key' => 'LAUNDRY_DIJEMPUT',
                'aliases' => ['LAUNDRY_DIJEMPUT', 'PICKED_UP', 'DRIVER_GOING_TO_PICKUP'],
                'label' => 'Laundry Dijemput Driver',
                'desc' => 'Pakaian kotor telah dijemput driver dan dalam perjalanan menuju outlet.',
            ],
            [
                'key' => 'LAUNDRY_DITERIMA',
                'aliases' => ['LAUNDRY_DITERIMA', 'RECEIVED_AT_OUTLET'],
                'label' => 'Laundry Tiba di Outlet',
                'desc' => 'Pakaian kotor telah diterima di outlet Washly.',
            ],
            [
                'key' => 'PROSES_LAUNDRY',
                'aliases' => ['PROSES_LAUNDRY', 'PROCESSING'],
                'label' => 'Proses Pencucian (Cuci & Setrika)',
                'desc' => 'Pakaian sedang diproses pencucian, pengeringan, dan penataan.',
            ],
            [
                'key' => 'LAUNDRY_SELESAI',
                'aliases' => ['LAUNDRY_SELESAI', 'PENIMBANGAN'],
                'label' => 'Pencucian Selesai & Penimbangan',
                'desc' => 'Proses pencucian selesai dan dilakukan penimbangan berat aktual.',
            ],
            [
                'key' => 'TAGIHAN_DIBUAT',
                'aliases' => ['TAGIHAN_DIBUAT', 'READY_FOR_DELIVERY'],
                'label' => 'Tagihan Diterbitkan',
                'desc' => 'Rincian total tagihan akhir telah diterbitkan oleh Admin.',
            ],
            [
                'key' => 'DRIVER_PENGIRIMAN_DITUGASKAN',
                'aliases' => ['DRIVER_PENGIRIMAN_DITUGASKAN', 'DELIVERY_ASSIGNED', 'DRIVER_GOING_TO_CUSTOMER'],
                'label' => 'Driver Pengiriman Ditugaskan',
                'desc' => 'Driver pengiriman telah ditugaskan dan sedang mengantar pakaian ke lokasi Anda.',
            ],
            [
                'key' => 'LAUNDRY_DIKEMBALIKAN',
                'aliases' => ['LAUNDRY_DIKEMBALIKAN', 'DELIVERED'],
                'label' => 'Laundry Diserahkan ke Customer',
                'desc' => 'Pakaian bersih dan rapi telah sampai dan diserahkan kepada Anda.',
            ],
            [
                'key' => 'ORDER_SELESAI',
                'aliases' => ['ORDER_SELESAI', 'PEMBAYARAN_DRIVER', 'COMPLETED'],
                'label' => 'Pesanan Selesai',
                'desc' => 'Pesanan laundry telah selesai sepenuhnya. Terima kasih!',
            ],
        ];

        // Determine active status index
        $currentStatus = $this->order->status;
        $currentStageIndex = 0;

        foreach ($timelineStages as $index => $stage) {
            if (in_array($currentStatus, $stage['aliases'], true) || $currentStatus === $stage['key']) {
                $currentStageIndex = $index;
                break;
            }
        }

        return view('livewire.customer.tracking', compact('timelineStages', 'currentStageIndex'))
            ->layout('components.layouts.app');
    }

    public function confirmOrderCompleted(\App\Services\OrderStatusService $statusService)
    {
        try {
            $statusService->transition(
                $this->order,
                'ORDER_SELESAI',
                Auth::user(),
                'Pesanan dikonfirmasi selesai oleh pelanggan.'
            );
            $this->order->completed_at = now();
            $this->order->save();
            $this->order->refresh();
            session()->flash('message', 'Terima kasih! Pesanan Anda telah dikonfirmasi selesai. Riwayat ini akan tersimpan selama 24 jam.');
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal mengonfirmasi pesanan: ' . $e->getMessage());
        }
    }
}


