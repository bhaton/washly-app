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
            session()->flash('message', "Status delivery order {$order->order_number} diubah ke Menuju Lokasi Customer.");
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
            session()->flash('message', "Delivery order {$order->order_number} berhasil diselesaikan. Pesanan dinyatakan SELESAI.");
            $this->showProofModal = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $driverId = Auth::id();

        $deliveryOrders = Order::where('delivery_driver_id', $driverId)
            ->whereIn('status', ['DELIVERY_ASSIGNED', 'DRIVER_GOING_TO_CUSTOMER', 'DELIVERED', 'COMPLETED'])
            ->with(['customer', 'orderItems'])
            ->latest()
            ->get();

        return view('livewire.driver.delivery-tasks', compact('deliveryOrders'))
            ->layout('components.layouts.app');
    }
}
