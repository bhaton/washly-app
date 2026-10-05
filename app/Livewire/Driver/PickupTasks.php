<?php

namespace App\Livewire\Driver;

use App\Models\Order;
use App\Services\PickupService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PickupTasks extends Component
{
    use WithFileUploads;

    public bool $showProofModal = false;
    public ?int $selectedOrderId = null;
    public $proofPhoto;
    public string $proofNotes = '';

    public function startPickup(int $orderId, PickupService $pickupService)
    {
        $order = Order::findOrFail($orderId);
        $driver = Auth::user();

        try {
            $pickupService->startPickup($order, $driver);
            session()->flash('message', "Status pickup order {$order->order_number} diubah ke Menuju Lokasi Pickup.");
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

    public function completePickup(PickupService $pickupService)
    {
        $this->validate([
            'proofPhoto' => 'required|image|max:5120', // Max 5MB
            'proofNotes' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($this->selectedOrderId);
        $driver = Auth::user();

        $path = $this->proofPhoto->store('pickup_proofs', 'public');

        try {
            $pickupService->completePickup($order, $driver, $path, $this->proofNotes);
            session()->flash('message', "Pickup order {$order->order_number} berhasil diselesaikan dengan bukti foto.");
            $this->showProofModal = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $driverId = Auth::id();

        $pickupOrders = Order::where('pickup_driver_id', $driverId)
            ->whereIn('status', ['DRIVER_DITUGASKAN', 'LAUNDRY_DIJEMPUT', 'PICKUP_ASSIGNED', 'DRIVER_GOING_TO_PICKUP'])
            ->with(['customer', 'orderItems', 'pickupProof'])
            ->latest()
            ->get();

        return view('livewire.driver.pickup-tasks', compact('pickupOrders'))
            ->layout('components.layouts.app');
    }
}
