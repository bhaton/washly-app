<?php

namespace App\Livewire\Driver;

use App\Models\Order;
use App\Services\PickupService;
use App\Services\DeliveryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaskDetail extends Component
{
    use WithFileUploads;

    public Order $order;
    public string $taskType = 'pickup'; // 'pickup' or 'delivery'
    public $proofPhoto;
    public string $proofNotes = '';
    public bool $showProofModal = false;

    public function mount(Order $order, string $type = 'pickup')
    {
        $driverId = Auth::id();

        // Enforce ownership check!
        if ($order->pickup_driver_id !== $driverId && $order->delivery_driver_id !== $driverId) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $this->order = $order->load(['customer', 'orderItems', 'pickupProof', 'deliveryProof']);
        $this->taskType = $type;
    }

    public function startTask(PickupService $pickupService, DeliveryService $deliveryService)
    {
        $driver = Auth::user();

        if ($this->taskType === 'pickup') {
            $pickupService->startPickup($this->order, $driver);
            session()->flash('message', 'Perjalanan pickup telah dimulai.');
        } else {
            $deliveryService->startDelivery($this->order, $driver);
            session()->flash('message', 'Perjalanan delivery telah dimulai.');
        }

        $this->order->refresh();
    }

    public function completeTask(PickupService $pickupService, DeliveryService $deliveryService)
    {
        $this->validate([
            'proofPhoto' => 'required|image|max:5120',
            'proofNotes' => 'nullable|string|max:500',
        ]);

        $driver = Auth::user();

        if ($this->taskType === 'pickup') {
            $path = $this->proofPhoto->store('pickup_proofs', 'public');
            $pickupService->completePickup($this->order, $driver, $path, $this->proofNotes);
            session()->flash('message', 'Tugas pickup selesai!');
        } else {
            $path = $this->proofPhoto->store('delivery_proofs', 'public');
            $deliveryService->completeDelivery($this->order, $driver, $path, $this->proofNotes);
            session()->flash('message', 'Tugas delivery selesai dan order COMPLETED!');
        }

        $this->showProofModal = false;
        $this->order->refresh();
    }

    public function render()
    {
        return view('livewire.driver.task-detail')
            ->layout('components.layouts.app');
    }
}
