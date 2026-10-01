<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customerId = Auth::id();

        $orders = Order::where('customer_id', $customerId)
            ->when(!empty($this->search), function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%');
            })
            ->when(!empty($this->statusFilter), function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->with(['orderItems', 'payment'])
            ->latest()
            ->paginate(10);

        return view('livewire.customer.orders', compact('orders'))
            ->layout('components.layouts.app');
    }
}
