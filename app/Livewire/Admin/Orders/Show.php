<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Models\User;
use App\Services\DriverAssignmentService;
use App\Services\OrderStatusService;
use App\Services\PricingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public Order $order;
    public ?int $selectedPickupDriverId = null;
    public ?int $selectedDeliveryDriverId = null;

    // Weighing input for admin
    public float|string $inputActualWeight = '';
    public bool $showReceiptModal = false;
    public bool $showDeleteModal = false;

    // Payment Proof Inspection
    public bool $showPaymentProofModal = false;
    public $uploadPaymentProofPhoto;

    // Manual status override by Admin
    public string $manualStatus = '';
    public string $manualNotes = '';

    public function mount(Order $order)
    {
        $this->order = $order->load([
            'customer',
            'pickupDriver',
            'deliveryDriver',
            'orderItems.service',
            'pickup.proof',
            'delivery.proof',
            'payment',
            'statusHistories.changedBy',
            'pickupProof',
            'deliveryProof'
        ]);

        $this->selectedPickupDriverId = $this->order->pickup_driver_id;
        $this->selectedDeliveryDriverId = $this->order->delivery_driver_id;
        $this->inputActualWeight = $this->order->actual_weight ?? $this->order->estimated_weight ?? '';
    }

    public function assignPickupDriver(DriverAssignmentService $assignmentService)
    {
        $this->validate([
            'selectedPickupDriverId' => 'required|exists:users,id',
        ]);

        try {
            $driver = User::findOrFail($this->selectedPickupDriverId);
            $assignmentService->assignPickupDriver($this->order, $driver, Auth::user());

            $this->order->refresh();
            session()->flash('message', "Driver pickup {$driver->name} berhasil ditugaskan.");
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal menugaskan driver pickup: {$e->getMessage()}");
        }
    }

    public function assignDeliveryDriver(DriverAssignmentService $assignmentService)
    {
        $this->validate([
            'selectedDeliveryDriverId' => 'required|exists:users,id',
        ]);

        try {
            $driver = User::findOrFail($this->selectedDeliveryDriverId);
            $assignmentService->assignDeliveryDriver($this->order, $driver, Auth::user());

            $this->order->refresh();
            session()->flash('message', "Driver delivery {$driver->name} berhasil ditugaskan.");
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal menugaskan driver delivery: {$e->getMessage()}");
        }
    }

    public function confirmLaundryPickedUp(OrderStatusService $statusService)
    {
        try {
            $statusService->transition($this->order, 'LAUNDRY_DIJEMPUT', Auth::user(), 'Laundry telah dijemput driver');
            $this->order->refresh();
            session()->flash('message', 'Status order diperbarui: Laundry telah dijemput driver.');
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal memperbarui status: {$e->getMessage()}");
        }
    }

    public function confirmLaundryReceived(OrderStatusService $statusService)
    {
        try {
            $statusService->transition($this->order, 'LAUNDRY_DITERIMA', Auth::user(), 'Laundry diterima di outlet');
            $statusService->transition($this->order, 'PROSES_LAUNDRY', Auth::user(), 'Mulai proses pencucian laundry');

            $this->order->refresh();
            session()->flash('message', 'Konfirmasi laundry diterima di outlet. Status berubah menjadi PROSES LAUNDRY.');
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal memperbarui status: {$e->getMessage()}");
        }
    }

    public function startLaundryProcessing(OrderStatusService $statusService)
    {
        try {
            $statusService->transition($this->order, 'PROSES_LAUNDRY', Auth::user(), 'Proses pencucian dimulai oleh Admin');
            $this->order->refresh();
            session()->flash('message', 'Status order diperbarui: PROSES LAUNDRY.');
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal memperbarui status: {$e->getMessage()}");
        }
    }

    public function finishLaundryProcessing(OrderStatusService $statusService)
    {
        try {
            $statusService->transition($this->order, 'LAUNDRY_SELESAI', Auth::user(), 'Proses pencucian selesai');
            $this->order->refresh();
            session()->flash('message', 'Status order diperbarui: LAUNDRY SELESAI.');
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal memperbarui status: {$e->getMessage()}");
        }
    }

    public function generateInvoice(OrderStatusService $statusService, PricingService $pricingService)
    {
        try {
            if ($this->order->service_type === 'kiloan') {
                $this->validate([
                    'inputActualWeight' => 'required|numeric|min:0.1',
                ]);

                $weight = (float) $this->inputActualWeight;
                $this->order->actual_weight = $weight;

                $pricePerKg = $pricingService->getKiloanPricePerKg(
                    $this->order->package_type ?? 'ekonomis',
                    $this->order->speed_type ?? 'reguler',
                    $this->order->wash_option ?? 'cuci_setrika'
                );

                $billWeight = max(1.0, $weight);
                $finalSubtotal = round($billWeight * $pricePerKg, 2);

                $this->order->subtotal = $finalSubtotal;
                $this->order->total = $finalSubtotal;
                $this->order->save();
            } else {
                $subtotal = 0.0;
                foreach ($this->order->orderItems as $item) {
                    $subtotal += $item->subtotal;
                }
                $this->order->subtotal = $subtotal;
                $this->order->total = $subtotal;
                $this->order->save();
            }

            if (in_array($this->order->status, ['PROSES_LAUNDRY', 'LAUNDRY_SELESAI', 'PENIMBANGAN', 'LAUNDRY_DITERIMA'])) {
                if (in_array($this->order->status, ['PROSES_LAUNDRY', 'LAUNDRY_DITERIMA'])) {
                    $statusService->transition($this->order, 'LAUNDRY_SELESAI', Auth::user(), 'Laundry selesai diproses');
                }
                if ($this->order->status === 'LAUNDRY_SELESAI') {
                    $statusService->transition($this->order, 'PENIMBANGAN', Auth::user(), 'Penimbangan akhir laundry');
                }
                $statusService->transition($this->order, 'TAGIHAN_DIBUAT', Auth::user(), 'Tagihan pembayaran berhasil dibuat secara otomatis oleh Admin berdasarkan berat faktual');
            }

            $this->order->refresh();
            session()->flash('message', 'Tagihan pembayaran faktual berhasil dibuat dan disimpan.');
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal membuat tagihan: {$e->getMessage()}");
        }
    }

    public function confirmDeliveryDone(OrderStatusService $statusService)
    {
        try {
            $statusService->transition($this->order, 'LAUNDRY_DIKEMBALIKAN', Auth::user(), 'Laundry dikembalikan / diserahkan kepada pelanggan');
            $this->order->refresh();
            session()->flash('message', 'Status order diperbarui: LAUNDRY DIKEMBALIKAN.');
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal memperbarui status: {$e->getMessage()}");
        }
    }

    public function confirmDriverPayment(OrderStatusService $statusService)
    {
        try {
            $statusService->transition($this->order, 'PEMBAYARAN_DRIVER', Auth::user(), 'Konfirmasi pembayaran driver');
            $this->order->refresh();
            session()->flash('message', 'Status order diperbarui: PEMBAYARAN DRIVER.');
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal memperbarui status: {$e->getMessage()}");
        }
    }

    public function completeOrder(OrderStatusService $statusService)
    {
        try {
            $statusService->transition($this->order, 'ORDER_SELESAI', Auth::user(), 'Order diselesaikan oleh Admin');
            $this->order->refresh();
            session()->flash('message', 'Status order diperbarui: ORDER SELESAI (Completed).');
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal menyelesaikan order: {$e->getMessage()}");
        }
    }

    public function updateStatusManual(OrderStatusService $statusService)
    {
        $this->validate([
            'manualStatus' => 'required|string',
        ]);

        try {
            $statusService->transition($this->order, $this->manualStatus, Auth::user(), $this->manualNotes ?: 'Update status manual oleh Admin');
            $this->order->refresh();
            $this->manualStatus = '';
            $this->manualNotes = '';
            session()->flash('message', 'Status order berhasil diperbarui secara manual.');
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal memperbarui status: {$e->getMessage()}");
        }
    }

    public function openDeleteModal()
    {
        $this->showDeleteModal = true;
    }

    public function deleteOrder()
    {
        try {
            $orderNum = $this->order->order_number;
            \Illuminate\Support\Facades\DB::transaction(function () {
                $this->order->orderItems()->delete();
                $this->order->statusHistories()->delete();
                if ($this->order->pickupProof) $this->order->pickupProof->delete();
                if ($this->order->deliveryProof) $this->order->deliveryProof->delete();
                if ($this->order->payment) $this->order->payment->delete();
                if ($this->order->pickup) $this->order->pickup->delete();
                if ($this->order->delivery) $this->order->delivery->delete();

                $this->order->delete();
            });
            session()->flash('message', "Order {$orderNum} berhasil dihapus.");
            return redirect()->route('admin.orders.index');
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal menghapus order: {$e->getMessage()}");
        }
    }

    public function markReceiptPrinted()
    {
        $this->order->receipt_printed_at = now();
        $this->order->save();
        $this->showReceiptModal = true;
    }

    public function openPaymentProofModal()
    {
        $this->showPaymentProofModal = true;
    }

    public function closePaymentProofModal()
    {
        $this->showPaymentProofModal = false;
    }

    public function confirmPaymentAsPaid(\App\Services\DemoQrisPaymentService $paymentService)
    {
        try {
            $paymentService->processSimulatedPayment($this->order, Auth::user(), 'Konfirmasi Manual (Admin)');
            $this->order->refresh();
            session()->flash('message', 'Status pembayaran berhasil dikonfirmasi LUNAS oleh Admin.');
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal mengonfirmasi pembayaran: ' . $e->getMessage());
        }
    }

    public function uploadPaymentProofPhoto()
    {
        $this->validate([
            'uploadPaymentProofPhoto' => 'required|image|max:5120',
        ]);

        try {
            $path = $this->uploadPaymentProofPhoto->store('payment-proofs', 'public');
            
            $payment = $this->order->payment;
            if (!$payment) {
                $payment = \App\Models\Payment::create([
                    'order_id' => $this->order->id,
                    'payment_method' => 'Transfer Bank (Bukti Foto)',
                    'payment_reference' => 'PAY-MANUAL-' . date('Ymd') . '-' . $this->order->id,
                    'amount' => $this->order->total > 0 ? $this->order->total : ($this->order->estimated_price ?? 0),
                    'status' => 'PAID',
                    'paid_at' => now(),
                    'proof_image' => $path,
                ]);
            } else {
                $payment->proof_image = $path;
                $payment->status = 'PAID';
                $payment->paid_at = $payment->paid_at ?? now();
                $payment->save();
            }

            $this->order->refresh();
            $this->uploadPaymentProofPhoto = null;
            session()->flash('message', 'Bukti foto pembayaran berhasil diunggah dan diverifikasi LUNAS!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal mengunggah bukti pembayaran: ' . $e->getMessage());
        }
    }


    public function render()
    {
        $drivers = User::role('driver')->where('is_active', true)->get();
        $statusLabels = OrderStatusService::$statusLabels;

        return view('livewire.admin.orders.show', compact('drivers', 'statusLabels'))
            ->layout('components.layouts.app');
    }
}

