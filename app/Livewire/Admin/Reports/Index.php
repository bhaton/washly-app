<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public string $period = 'month'; // 'today', 'week', 'month', 'all'

    public function render()
    {
        $query = Order::query();
        $paymentQuery = Payment::where('status', 'PAID');

        if ($this->period === 'today') {
            $startDate = now()->startOfDay();
            $query->where('created_at', '>=', $startDate);
            $paymentQuery->where('paid_at', '>=', $startDate);
        } elseif ($this->period === 'week') {
            $startDate = now()->startOfWeek();
            $query->where('created_at', '>=', $startDate);
            $paymentQuery->where('paid_at', '>=', $startDate);
        } elseif ($this->period === 'month') {
            $startDate = now()->startOfMonth();
            $query->where('created_at', '>=', $startDate);
            $paymentQuery->where('paid_at', '>=', $startDate);
        }

        $totalRevenue = $paymentQuery->sum('amount');
        $totalOrders = (clone $query)->count();
        $completedOrders = (clone $query)->where('status', 'COMPLETED')->count();
        $cancelledOrders = (clone $query)->where('status', 'CANCELLED')->count();

        // Top Items
        $topItems = OrderItem::selectRaw('service_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('service_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Driver Performance
        $drivers = User::role('driver')
            ->withCount(['pickupTasks as completed_pickups' => function ($q) {
                $q->where('status', 'COMPLETED');
            }])
            ->withCount(['deliveryTasks as completed_deliveries' => function ($q) {
                $q->where('status', 'COMPLETED');
            }])
            ->get();

        return view('livewire.admin.reports.index', compact(
            'totalRevenue',
            'totalOrders',
            'completedOrders',
            'cancelledOrders',
            'topItems',
            'drivers'
        ))->layout('components.layouts.app');
    }
}
