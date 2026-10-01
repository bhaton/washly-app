<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderDetail extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses detail pesanan ini.');
        }

        $this->order = $order->load([
            'orderItems.service',
            'payment',
            'pickupDriver',
            'deliveryDriver',
            'pickup.proof',
            'delivery.proof',
            'statusHistories'
        ]);
    }

    public function render()
    {
        return view('livewire.customer.order-detail')
            ->layout('components.layouts.app');
    }
}
