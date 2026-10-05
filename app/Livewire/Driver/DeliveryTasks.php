<?php

namespace App\Livewire\Driver;

use App\Models\Order;
use App\Services\DeliveryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class DeliveryTasks extends Component
{
    use WithFileUploads;

    public bool $showProofModal = false;
    public ?int $selectedOrderId = null;
    public $proofPhoto;
    public string $proofNotes = '';

    public function startDelivery(int $orderId, DeliveryService $deliveryService)
    {
        $order = Order::findOrFail($orderId);
        $driver = Auth::user();

        try {
            $deliveryService->startDelivery($order, $driver);
            session()->flash('message', "Status delivery order {$order->order_number} diubah ke Laundry Dikembalikan.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openProofModal(int $orderId)
    {
        $this->selectedOrderId = $orderId;
        $this->reset(['proofPhoto', 'proofNotes']);
        $this->showProofModal = true;
    }

    public function completeDelivery(DeliveryService $deliveryService)
    {
        $this->validate([
            'proofPhoto' => 'required|image|max:5120',
            'proofNotes' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($this->selectedOrderId);
        $driver = Auth::user();

        $path = $this->proofPhoto->store('delivery_proofs', 'public');

        try {
            $deliveryService->completeDelivery($order, $driver, $path, $this->proofNotes);
            session()->flash('message', "Delivery order {$order->order_number} berhasil diselesaikan. Laundry diserahkan.");
            $this->showProofModal = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function confirmPayment(int $orderId, DeliveryService $deliveryService)
    {
        $order = Order::findOrFail($orderId);
        $driver = Auth::user();

        try {
            $deliveryService->confirmPaymentReceived($order, $driver);
            session()->flash('message', "Konfirmasi pembayaran driver berhasil untuk order {$order->order_number}. Order Selesai!");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $driverId = Auth::id();

        $deliveryOrders = Order::where('delivery_driver_id', $driverId)
            ->whereIn('status', ['DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN', 'PEMBAYARAN_DRIVER', 'DELIVERY_ASSIGNED', 'DRIVER_GOING_TO_CUSTOMER'])
            ->with(['customer', 'orderItems', 'deliveryProof'])
            ->latest()
            ->get();

        return view('livewire.driver.delivery-tasks', compact('deliveryOrders'))
            ->layout('components.layouts.app');
    }
}
