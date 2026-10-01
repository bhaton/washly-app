<?php

namespace App\Livewire\Admin\Customers;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = User::role('customer')
            ->when(!empty($this->search), function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount('customerOrders')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.customers.index', compact('customers'))
            ->layout('components.layouts.app');
    }
}
