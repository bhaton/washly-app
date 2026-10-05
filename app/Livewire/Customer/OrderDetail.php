<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use App\Services\DemoQrisPaymentService;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderDetail extends Component
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
            abort(403, 'Anda tidak berhak mengakses detail pesanan ini.');
        }

        $this->order = $order->load([
            'orderItems.service',
            'payment',
            'pickupDriver',
            'deliveryDriver',
            'pickup.proof',
            'delivery.proof',
            'statusHistories'
        ]);
    }

    public function confirmOrderCompleted(OrderStatusService $statusService)
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

    public function render()
    {
        return view('livewire.customer.order-detail')
            ->layout('components.layouts.app');
    }
}


