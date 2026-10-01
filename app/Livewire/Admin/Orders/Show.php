<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Models\User;
use App\Services\DriverAssignmentService;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Order $order;
    public ?int $selectedPickupDriverId = null;
    public ?int $selectedDeliveryDriverId = null;

    public function mount(Order $order)
    {
        $this->order = $order->load([
            'customer',
            'pickupDriver',
            'deliveryDriver',
            'orderItems.service',
            'pickup.proof',
            'delivery.proof',
            'payment',
            'statusHistories.changedBy',
            'pickupProof',
            'deliveryProof'
        ]);

        $this->selectedPickupDriverId = $this->order->pickup_driver_id;
        $this->selectedDeliveryDriverId = $this->order->delivery_driver_id;
    }

    public function assignPickupDriver(DriverAssignmentService $assignmentService)
    {
        $this->validate([
            'selectedPickupDriverId' => 'required|exists:users,id',
        ]);

        $driver = User::findOrFail($this->selectedPickupDriverId);
        $assignmentService->assignPickupDriver($this->order, $driver, Auth::user());
        
        $this->order->refresh();
        session()->flash('message', "Driver pickup {$driver->name} berhasil ditugaskan.");
    }

    public function assignDeliveryDriver(DriverAssignmentService $assignmentService)
    {
        $this->validate([
            'selectedDeliveryDriverId' => 'required|exists:users,id',
        ]);

        $driver = User::findOrFail($this->selectedDeliveryDriverId);
        $assignmentService->assignDeliveryDriver($this->order, $driver, Auth::user());

        $this->order->refresh();
        session()->flash('message', "Driver delivery {$driver->name} berhasil ditugaskan.");
    }

    public function transitionTo(string $targetStatus, OrderStatusService $statusService)
    {
        $statusService->transition($this->order, $targetStatus, Auth::user(), "Perubahan status dari detail order oleh Admin");
        $this->order->refresh();
        session()->flash('message', "Status order berhasil diubah menjadi {$targetStatus}.");
    }

    public function render()
    {
        $drivers = User::role('driver')->where('is_active', true)->get();

        return view('livewire.admin.orders.show', compact('drivers'))
            ->layout('components.layouts.app');
    }
}
