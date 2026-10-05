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

        $activePickupStatuses = ['DRIVER_DITUGASKAN', 'LAUNDRY_DIJEMPUT', 'PICKUP_ASSIGNED', 'DRIVER_GOING_TO_PICKUP'];
        $activeDeliveryStatuses = ['DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN', 'DELIVERY_ASSIGNED', 'DRIVER_GOING_TO_CUSTOMER'];

        $assignedPickupsCount = Order::where('pickup_driver_id', $driverId)
            ->whereIn('status', $activePickupStatuses)
            ->count();

        $assignedDeliveriesCount = Order::where('delivery_driver_id', $driverId)
            ->whereIn('status', $activeDeliveryStatuses)
            ->count();

        $completedPickupsCount = Order::where('pickup_driver_id', $driverId)
            ->whereNotIn('status', array_merge($activePickupStatuses, ['MENUNGGU_PICKUP', 'CANCELLED']))
            ->count();

        $completedDeliveriesCount = Order::where('delivery_driver_id', $driverId)
            ->whereIn('status', ['PEMBAYARAN_DRIVER', 'ORDER_SELESAI', 'DELIVERED', 'COMPLETED'])
            ->count();

        $pendingPickups = Order::where('pickup_driver_id', $driverId)
            ->whereIn('status', $activePickupStatuses)
            ->with('customer')
            ->latest()
            ->get();

        $pendingDeliveries = Order::where('delivery_driver_id', $driverId)
            ->whereIn('status', $activeDeliveryStatuses)
            ->with('customer')
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
