<?php

namespace App\Livewire\Admin\Payments;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function render()
    {
        $payments = Payment::with(['order.customer'])
            ->when(!empty($this->search), function ($q) {
                $q->where('payment_reference', 'like', '%' . $this->search . '%')
                  ->orWhereHas('order', function ($oq) {
                      $oq->where('order_number', 'like', '%' . $this->search . '%');
                  });
            })
            ->when(!empty($this->statusFilter), function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.payments.index', compact('payments'))
            ->layout('components.layouts.app');
    }
}
