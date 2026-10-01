<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use App\Models\Service;
use App\Services\DemoQrisPaymentService;
use App\Services\OrderService;
use App\Services\PricingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateOrder extends Component
{
    public int $step = 1;

    // Step 1: Selected items [ service_id => quantity ]
    public array $quantities = [];
    public string $step1_error = '';

    // Step 2: Pickup details
    public string $pickup_name = '';
    public string $pickup_phone = '';
    public string $pickup_address = '';
    public string $pickup_date = '';
    public string $pickup_time = '09:00';
    public string $pickup_notes = '';

    // Step 3: Delivery details
    public bool $sameAsPickup = true;
    public string $delivery_name = '';
    public string $delivery_phone = '';
    public string $delivery_address = '';
    public string $delivery_notes = '';

    // Step 4 & 5 state
    public ?Order $createdOrder = null;
    public string $paymentMethod = 'QRIS';

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->pickup_name = $user->name;
            $this->pickup_phone = $user->phone ?? '';
            $this->pickup_address = $user->address ?? '';
            $this->delivery_name = $user->name;
            $this->delivery_phone = $user->phone ?? '';
            $this->delivery_address = $user->address ?? '';
        }
        $this->pickup_date = date('Y-m-d', strtotime('+1 day'));

        $services = Service::where('is_active', true)->get();
        foreach ($services as $service) {
            $this->quantities[$service->id] = 0;
        }
    }

    public function updateQuantity($serviceId, $value)
    {
        $sId = (int) $serviceId;
        $val = max(0, (int) $value);
        $this->quantities[$sId] = $val;
        $this->step1_error = '';
    }

    public function incrementQuantity($serviceId)
    {
        $sId = (int) $serviceId;
        $current = (int) ($this->quantities[$sId] ?? 0);
        $this->quantities[$sId] = $current + 1;
        $this->step1_error = '';
    }

    public function decrementQuantity($serviceId)
    {
        $sId = (int) $serviceId;
        $current = (int) ($this->quantities[$sId] ?? 0);
        if ($current > 0) {
            $this->quantities[$sId] = $current - 1;
        }
    }

    public function goToStep2()
    {
        $selectedCount = 0;
        foreach ($this->quantities as $id => $qty) {
            $selectedCount += (int) $qty;
        }

        if ($selectedCount <= 0) {
            $this->step1_error = 'Pilih minimal 1 item laundry (kuantitas > 0) dengan menekan tombol (+) untuk melanjutkan ke lokasi pickup.';
            session()->flash('step1_error', $this->step1_error);
            return;
        }

        $this->step1_error = '';
        $this->step = 2;
    }

    public function goToStep3()
    {
        $this->validate([
            'pickup_name' => 'required|string|max:255',
            'pickup_phone' => 'required|string|max:20',
            'pickup_address' => 'required|string|max:500',
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|string',
            'pickup_notes' => 'nullable|string|max:500',
        ]);

        if ($this->sameAsPickup) {
            $this->delivery_name = $this->pickup_name;
            $this->delivery_phone = $this->pickup_phone;
            $this->delivery_address = $this->pickup_address;
        }

        $this->step = 3;
    }

    public function goToStep4()
    {
        if (!$this->sameAsPickup) {
            $this->validate([
                'delivery_name' => 'required|string|max:255',
                'delivery_phone' => 'required|string|max:20',
                'delivery_address' => 'required|string|max:500',
                'delivery_notes' => 'nullable|string|max:500',
            ]);
        }
        $this->step = 4;
    }

    public function submitOrder(OrderService $orderService, DemoQrisPaymentService $paymentService)
    {
        $itemsData = [];
        foreach ($this->quantities as $serviceId => $qty) {
            $quantity = (int) $qty;
            if ($quantity > 0) {
                $itemsData[] = [
                    'service_id' => (int) $serviceId,
                    'quantity' => $quantity,
                ];
            }
        }

        $pickupData = [
            'pickup_name' => $this->pickup_name,
            'pickup_phone' => $this->pickup_phone,
            'pickup_address' => $this->pickup_address,
            'pickup_date' => $this->pickup_date,
            'pickup_time' => $this->pickup_time,
            'pickup_notes' => $this->pickup_notes,
        ];

        $deliveryData = [
            'delivery_name' => $this->sameAsPickup ? $this->pickup_name : $this->delivery_name,
            'delivery_phone' => $this->sameAsPickup ? $this->pickup_phone : $this->delivery_phone,
            'delivery_address' => $this->sameAsPickup ? $this->pickup_address : $this->delivery_address,
            'delivery_notes' => $this->delivery_notes,
        ];

        $this->createdOrder = $orderService->createOrder(
            Auth::user(),
            $itemsData,
            $pickupData,
            $deliveryData
        );

        $paymentService->createPayment($this->createdOrder);

        $this->step = 5;
    }

    public function processDemoPayment(DemoQrisPaymentService $paymentService)
    {
        if (!$this->createdOrder) {
            return;
        }

        $paymentService->processSimulatedPayment($this->createdOrder, Auth::user());

        session()->flash('message', 'Pembayaran QRIS Demo berhasil! Pesanan Anda telah dikonfirmasi dan sedang diproses.');

        return redirect()->route('customer.tracking', ['order' => $this->createdOrder->id]);
    }

    public function render(PricingService $pricingService)
    {
        $services = Service::where('is_active', true)->get();

        $selectedItems = [];
        $pricingItemsPayload = [];

        foreach ($services as $service) {
            $qty = (int) ($this->quantities[$service->id] ?? 0);
            if ($qty > 0) {
                $subtotal = $service->price * $qty;
                $selectedItems[] = [
                    'service' => $service,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];
                $pricingItemsPayload[] = [
                    'price' => (float) $service->price,
                    'quantity' => $qty,
                ];
            }
        }

        $totals = $pricingService->calculate($pricingItemsPayload);

        return view('livewire.customer.create-order', compact('services', 'selectedItems', 'totals'))
            ->layout('components.layouts.app');
    }
}
