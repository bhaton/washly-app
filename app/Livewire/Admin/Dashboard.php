<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\Payment;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $today = now()->startOfDay();

        $todayOrdersCount = Order::where('created_at', '>=', $today)->count();
        $waitingConfirmationCount = Order::where('status', 'WAITING_CONFIRMATION')->count();
        $waitingPickupCount = Order::whereIn('status', ['WAITING_PICKUP', 'PICKUP_ASSIGNED'])->count();
        $processingCount = Order::where('status', 'PROCESSING')->count();
        $readyDeliveryCount = Order::where('status', 'READY_FOR_DELIVERY')->count();
        $activeDeliveryCount = Order::whereIn('status', ['DELIVERY_ASSIGNED', 'DRIVER_GOING_TO_CUSTOMER'])->count();
        $completedCount = Order::where('status', 'COMPLETED')->count();
        $totalRevenue = Payment::where('status', 'PAID')->sum('amount');

        $recentOrders = Order::with(['customer', 'pickupDriver', 'deliveryDriver'])
            ->latest()
            ->take(10)
            ->get();

        try {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'driver']);
            $activeDrivers = User::role('driver')
                ->where('is_active', true)
                ->withCount(['pickupTasks as active_pickups_count' => function ($query) {
                    $query->whereIn('status', ['ASSIGNED', 'IN_PROGRESS']);
                }])
                ->withCount(['deliveryTasks as active_deliveries_count' => function ($query) {
                    $query->whereIn('status', ['ASSIGNED', 'IN_PROGRESS']);
                }])
                ->get();
        } catch (\Throwable $e) {
            $activeDrivers = collect();
        }

        return view('livewire.admin.dashboard', compact(
            'todayOrdersCount',
            'waitingConfirmationCount',
            'waitingPickupCount',
            'processingCount',
            'readyDeliveryCount',
            'activeDeliveryCount',
            'completedCount',
            'totalRevenue',
            'recentOrders',
            'activeDrivers'
        ))->layout('components.layouts.app');
    }
}
