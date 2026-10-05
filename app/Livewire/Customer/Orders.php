<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public bool $showReceiptModal = false;
    public ?Order $selectedReceiptOrder = null;

    public bool $showPaymentModal = false;
    public ?Order $selectedPaymentOrder = null;
    public string $selectedPaymentMethod = 'QRIS';

    public function openReceiptModal(int $orderId)
    {
        $this->selectedReceiptOrder = Order::where('customer_id', Auth::id())
            ->with(['orderItems', 'customer', 'pickupDriver', 'deliveryDriver', 'payment'])
            ->findOrFail($orderId);
        
        if (!$this->selectedReceiptOrder->receipt_printed_at) {
            $this->selectedReceiptOrder->receipt_printed_at = now();
            $this->selectedReceiptOrder->save();
        }

        $this->showReceiptModal = true;
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->selectedReceiptOrder = null;
    }

    public function openPaymentModal(int $orderId)
    {
        $this->selectedPaymentOrder = Order::where('customer_id', Auth::id())
            ->with(['payment'])
            ->findOrFail($orderId);
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedPaymentOrder = null;
    }

    public function selectPaymentMethod(string $method)
    {
        $this->selectedPaymentMethod = $method;
    }

    public function processGatewayPayment(\App\Services\DemoQrisPaymentService $paymentService)
    {
        if (!$this->selectedPaymentOrder) {
            return;
        }

        $isPaymentAllowed = in_array($this->selectedPaymentOrder->status, ['LAUNDRY_SELESAI', 'PENIMBANGAN', 'TAGIHAN_DIBUAT', 'READY_FOR_DELIVERY', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN']);
        if (!$isPaymentAllowed) {
            session()->flash('error', 'Pembayaran hanya dapat dilakukan setelah proses laundry selesai ditimbang dan sebelum tahap pengiriman driver.');
            return;
        }

        try {
            $paymentService->processSimulatedPayment($this->selectedPaymentOrder, Auth::user(), $this->selectedPaymentMethod);
            $this->selectedPaymentOrder->refresh();
            session()->flash('message', '✓ Pembayaran via Gateway (' . $this->selectedPaymentMethod . ') untuk order ' . $this->selectedPaymentOrder->order_number . ' berhasil dikonfirmasi!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function payWithMidtrans(\App\Services\MidtransService $midtransService)
    {
        if (!$this->selectedPaymentOrder) {
            return;
        }

        $isPaymentAllowed = in_array($this->selectedPaymentOrder->status, ['LAUNDRY_SELESAI', 'PENIMBANGAN', 'TAGIHAN_DIBUAT', 'READY_FOR_DELIVERY', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN']);
        if (!$isPaymentAllowed) {
            session()->flash('error', 'Pembayaran hanya dapat dilakukan setelah proses laundry selesai ditimbang dan sebelum tahap pengiriman driver.');
            return;
        }

        try {
            $result = $midtransService->createSnapToken($this->selectedPaymentOrder);
            if (!empty($result['snap_token'])) {
                $this->dispatch('trigger-midtrans-pay', token: $result['snap_token'], orderNumber: $this->selectedPaymentOrder->order_number);
                session()->flash('message', 'Membuka Midtrans Snap Payment Gateway...');
            }
        } catch (\Throwable $e) {
            session()->flash('error', 'Midtrans Gateway Error: ' . $e->getMessage());
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function confirmOrderCompleted($orderId, OrderStatusService $statusService)
    {
        $order = Order::where('customer_id', Auth::id())->findOrFail($orderId);
        try {
            $statusService->transition(
                $order,
                'ORDER_SELESAI',
                Auth::user(),
                'Pesanan dikonfirmasi selesai oleh pelanggan.'
            );
            $order->completed_at = now();
            $order->save();
            session()->flash('message', "Pesanan {$order->order_number} berhasil dikonfirmasi selesai.");
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal mengonfirmasi pesanan: ' . $e->getMessage());
        }
    }

    public function deleteOrder($orderId)
    {
        try {
            $order = Order::where('customer_id', Auth::id())->findOrFail($orderId);
            \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                $orderNum = $order->order_number;
                $order->orderItems()->delete();
                $order->statusHistories()->delete();
                if ($order->pickupProof) $order->pickupProof->delete();
                if ($order->deliveryProof) $order->deliveryProof->delete();
                if ($order->payment) $order->payment->delete();
                if ($order->pickup) $order->pickup->delete();
                if ($order->delivery) $order->delivery->delete();
                $order->delete();
                session()->flash('message', "Pesanan {$orderNum} berhasil dihapus.");
            });
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menghapus pesanan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $customerId = Auth::id();
        $cutoff24h = now()->subHours(24);

        $orders = Order::where('customer_id', $customerId)
            ->where(function ($query) use ($cutoff24h) {
                // Show all active (non-completed / non-cancelled) orders
                $query->whereNotIn('status', ['ORDER_SELESAI', 'COMPLETED', 'CANCELLED'])
                    // OR completed orders finished within the last 24 hours
                    ->orWhere(function ($q) use ($cutoff24h) {
                        $q->whereIn('status', ['ORDER_SELESAI', 'COMPLETED'])
                          ->where(function ($sub) use ($cutoff24h) {
                              $sub->where('completed_at', '>=', $cutoff24h)
                                  ->orWhere(function ($sub2) use ($cutoff24h) {
                                      $sub2->whereNull('completed_at')
                                           ->where('updated_at', '>=', $cutoff24h);
                                  });
                          });
                    });
            })
            ->when(!empty($this->search), function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%');
            })
            ->when(!empty($this->statusFilter), function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->with(['orderItems', 'payment'])
            ->latest()
            ->paginate(10);

        return view('livewire.customer.orders', compact('orders'))
            ->layout('components.layouts.app');
    }
}

