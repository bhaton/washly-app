<?php

namespace App\Livewire\Driver;

use App\Models\Order;
use App\Models\Pickup;
use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $driverId = Auth::id();

        $assignedPickupsCount = Order::where('pickup_driver_id', $driverId)
            ->whereIn('status', ['PICKUP_ASSIGNED', 'DRIVER_GOING_TO_PICKUP'])
            ->count();

        $assignedDeliveriesCount = Order::where('delivery_driver_id', $driverId)
            ->whereIn('status', ['DELIVERY_ASSIGNED', 'DRIVER_GOING_TO_CUSTOMER'])
            ->count();

        $completedPickupsCount = Pickup::where('driver_id', $driverId)
            ->where('status', 'COMPLETED')
            ->count();

        $completedDeliveriesCount = Delivery::where('driver_id', $driverId)
            ->where('status', 'COMPLETED')
            ->count();

        $pendingPickups = Order::where('pickup_driver_id', $driverId)
            ->whereIn('status', ['PICKUP_ASSIGNED', 'DRIVER_GOING_TO_PICKUP'])
            ->latest()
            ->get();

        $pendingDeliveries = Order::where('delivery_driver_id', $driverId)
            ->whereIn('status', ['DELIVERY_ASSIGNED', 'DRIVER_GOING_TO_CUSTOMER'])
            ->latest()
            ->get();

        return view('livewire.driver.dashboard', compact(
            'assignedPickupsCount',
            'assignedDeliveriesCount',
            'completedPickupsCount',
            'completedDeliveriesCount',
            'pendingPickups',
            'pendingDeliveries'
        ))->layout('components.layouts.app');
    }
}
