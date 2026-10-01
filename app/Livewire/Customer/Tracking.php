<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Tracking extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tracking pesanan ini.');
        }

        $this->order = $order->load([
            'payment',
            'pickupDriver',
            'deliveryDriver',
            'statusHistories'
        ]);
    }

    public function render()
    {
        // Timeline status order mapping
        $stages = [
            'PAID' => ['label' => 'Pembayaran Berhasil', 'desc' => 'Pembayaran via QRIS Demo telah diterima.'],
            'CONFIRMED' => ['label' => 'Order Dikonfirmasi', 'desc' => 'Pesanan telah diverifikasi oleh outlet.'],
            'PICKUP_ASSIGNED' => ['label' => 'Driver Pickup Ditugaskan', 'desc' => 'Driver sedang bersiap melakukan penjemputan.'],
            'PICKED_UP' => ['label' => 'Laundry Dijemput', 'desc' => 'Laundry telah diterima driver.'],
            'RECEIVED_AT_OUTLET' => ['label' => 'Diterima di Outlet', 'desc' => 'Pakaian telah tiba di tempat pencucian.'],
            'PROCESSING' => ['label' => 'Sedang Diproses', 'desc' => 'Proses pencucian dan penyetrikaan berlangsung.'],
            'READY_FOR_DELIVERY' => ['label' => 'Siap Diantar', 'desc' => 'Pakaian bersih dan rapi siap diantarkan.'],
            'DRIVER_GOING_TO_CUSTOMER' => ['label' => 'Driver Menuju Customer', 'desc' => 'Driver dalam perjalanan ke alamat tujuan.'],
            'COMPLETED' => ['label' => 'Pesanan Selesai', 'desc' => 'Laundry telah diterima dengan baik. Terima kasih!'],
        ];

        return view('livewire.customer.tracking', compact('stages'))
            ->layout('components.layouts.app');
    }
}
