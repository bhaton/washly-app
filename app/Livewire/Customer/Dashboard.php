<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $customerId = Auth::id();

        $activeOrdersCount = Order::where('customer_id', $customerId)
            ->whereNotIn('status', ['COMPLETED', 'CANCELLED'])
            ->count();

        $completedOrdersCount = Order::where('customer_id', $customerId)
            ->where('status', 'COMPLETED')
            ->count();

        $totalSpent = Order::where('customer_id', $customerId)
            ->whereHas('payment', function ($q) {
                $q->where('status', 'PAID');
            })
            ->sum('total');

        $activeOrders = Order::where('customer_id', $customerId)
            ->whereNotIn('status', ['COMPLETED', 'CANCELLED'])
            ->with(['orderItems', 'pickupDriver', 'deliveryDriver'])
            ->latest()
            ->get();

        $recentOrders = Order::where('customer_id', $customerId)
            ->with(['orderItems'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.customer.dashboard', compact(
            'activeOrdersCount',
            'completedOrdersCount',
            'totalSpent',
            'activeOrders',
            'recentOrders'
        ))->layout('components.layouts.app');
    }
}
