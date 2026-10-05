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
        $cutoff24h = now()->subHours(24);

        $activeOrdersCount = Order::where('customer_id', $customerId)
            ->whereNotIn('status', ['ORDER_SELESAI', 'COMPLETED', 'CANCELLED'])
            ->count();

        $completedOrdersCount = Order::where('customer_id', $customerId)
            ->whereIn('status', ['ORDER_SELESAI', 'COMPLETED'])
            ->count();

        $totalSpent = Order::where('customer_id', $customerId)
            ->whereHas('payment', function ($q) {
                $q->where('status', 'PAID');
            })
            ->sum('total');

        $activeOrders = Order::where('customer_id', $customerId)
            ->whereNotIn('status', ['ORDER_SELESAI', 'COMPLETED', 'CANCELLED'])
            ->with(['orderItems', 'pickupDriver', 'deliveryDriver'])
            ->latest()
            ->get();

        $recentOrders = Order::where('customer_id', $customerId)
            ->where(function ($query) use ($cutoff24h) {
                $query->whereNotIn('status', ['ORDER_SELESAI', 'COMPLETED', 'CANCELLED'])
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

