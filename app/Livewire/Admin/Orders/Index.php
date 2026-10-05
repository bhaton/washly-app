<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Models\User;
use App\Services\DriverAssignmentService;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $dateFilter = '';

    // Assign Modal state
    public bool $showAssignModal = false;
    public ?int $selectedOrderId = null;
    public string $assignType = 'pickup'; // 'pickup' or 'delivery'
    public ?int $selectedDriverId = null;

    // Delete Modal state
    public bool $showDeleteModal = false;
    public ?int $orderIdToDelete = null;
    public ?string $orderNumberToDelete = '';

    protected $queryString = ['search', 'statusFilter', 'dateFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function confirmOrder(int $orderId, OrderStatusService $statusService)
    {
        $order = Order::findOrFail($orderId);
        $admin = Auth::user();

        if (in_array($order->status, ['PAID', 'WAITING_CONFIRMATION'])) {
            $statusService->transition($order, 'WAITING_CONFIRMATION', $admin);
            $statusService->transition($order, 'CONFIRMED', $admin, 'Pesanan dikonfirmasi oleh outlet');
            $statusService->transition($order, 'WAITING_PICKUP', $admin, 'Menunggu penugasan driver pickup');
            session()->flash('message', "Pesanan {$order->order_number} berhasil dikonfirmasi.");
        }
    }

    public function openAssignModal(int $orderId, string $type)
    {
        $this->selectedOrderId = $orderId;
        $this->assignType = $type;
        $this->selectedDriverId = null;
        $this->showAssignModal = true;
    }

    public function assignDriver(DriverAssignmentService $assignmentService)
    {
        $this->validate([
            'selectedDriverId' => 'required|exists:users,id',
        ]);

        $order = Order::findOrFail($this->selectedOrderId);
        $driver = User::findOrFail($this->selectedDriverId);
        $admin = Auth::user();

        if ($this->assignType === 'pickup') {
            $assignmentService->assignPickupDriver($order, $driver, $admin);
            session()->flash('message', "Driver pickup {$driver->name} berhasil ditugaskan untuk order {$order->order_number}.");
        } else {
            $assignmentService->assignDeliveryDriver($order, $driver, $admin);
            session()->flash('message', "Driver delivery {$driver->name} berhasil ditugaskan untuk order {$order->order_number}.");
        }

        $this->showAssignModal = false;
    }

    public function updateStatus(int $orderId, string $targetStatus, OrderStatusService $statusService)
    {
        $order = Order::findOrFail($orderId);
        $admin = Auth::user();

        $statusService->transition($order, $targetStatus, $admin, "Status diubah oleh admin");
        session()->flash('message', "Status order {$order->order_number} berhasil diubah ke {$targetStatus}.");
    }

    public function confirmDelete(int $orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $this->orderIdToDelete = $order->id;
            $this->orderNumberToDelete = $order->order_number;
            $this->showDeleteModal = true;
        }
    }

    public function deleteOrder()
    {
        if (!$this->orderIdToDelete) {
            return;
        }

        try {
            $orderId = $this->orderIdToDelete;
            \Illuminate\Support\Facades\DB::transaction(function () use ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $orderNum = $order->order_number;
                    $order->orderItems()->delete();
                    $order->statusHistories()->delete();
                    $order->pickupProof()->delete();
                    $order->deliveryProof()->delete();
                    $order->payment()->delete();
                    $order->pickup()->delete();
                    $order->delivery()->delete();
                    $order->delete();
                    session()->flash('message', "Order {$orderNum} berhasil dihapus.");
                }
            });
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal menghapus order: {$e->getMessage()}");
        } finally {
            $this->showDeleteModal = false;
            $this->orderIdToDelete = null;
            $this->orderNumberToDelete = '';
        }
    }

    public function render()
    {
        $query = Order::with(['customer', 'pickupDriver', 'deliveryDriver', 'orderItems', 'payment']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->dateFilter)) {
            if ($this->dateFilter === 'today') {
                $query->whereDate('created_at', now()->today());
            } elseif ($this->dateFilter === 'this_week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($this->dateFilter === 'this_month') {
                $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            }
        }

        $orders = $query->latest()->paginate(10);
        $drivers = User::role('driver')->where('is_active', true)->get();

        return view('livewire.admin.orders.index', compact('orders', 'drivers'))
            ->layout('components.layouts.app');
    }
}

