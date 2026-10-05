<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use App\Models\Service;
use App\Services\OrderService;
use App\Services\PricingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateOrder extends Component
{
    public int $step = 1;

    // Service Selection
    public string $service_type = 'kiloan'; // 'kiloan' or 'per_item'
    public string $package_type = 'ekonomis'; // 'ekonomis' or 'premium'
    public string $speed_type = 'reguler'; // 'reguler' or 'ekspres'
    public string $wash_option = 'cuci_setrika'; // 'cuci_setrika', 'cuci_lipat', 'setrika_saja'

    // Quantities for items [ service_id => quantity ]
    public array $quantities = [];
    public string $step1_error = '';

    // Action Toast / Notification
    public ?array $notification = null; // ['type' => 'success'|'warning'|'error'|'info', 'message' => string]

    // Custom Item (for "Item Lainnya")
    public bool $has_custom_item = false;
    public string $custom_item_name = '';
    public int $custom_item_qty = 1;
    public string $custom_item_notes = '';

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
    public string $delivery_date = '';
    public string $delivery_time = '14:00';
    public string $delivery_notes = '';

    // Order created
    public ?Order $createdOrder = null;

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
        $this->delivery_date = date('Y-m-d', strtotime('+3 days'));

        $services = Service::where('is_active', true)->get();
        foreach ($services as $service) {
            $this->quantities[$service->id] = 0;
        }
    }

    public function notify(string $message, string $type = 'success'): void
    {
        $this->notification = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public function dismissNotification(): void
    {
        $this->notification = null;
    }

    public function selectServiceType(string $type)
    {
        if ($this->service_type === $type) {
            return;
        }

        $this->service_type = $type;
        $this->step1_error = '';

        // Check for incompatible item selections and clear them
        $allServices = Service::where('is_active', true)->get()->keyBy('id');
        $removedCount = 0;

        foreach ($this->quantities as $serviceId => $qty) {
            if ((int) $qty > 0) {
                $service = $allServices->get($serviceId);
                if ($service) {
                    $isEligible = $type === 'kiloan' ? $service->isKiloanEligible() : $service->isSatuanEligible();
                    if (!$isEligible) {
                        $this->quantities[$serviceId] = 0;
                        $removedCount++;
                    }
                }
            }
        }

        $serviceLabel = $type === 'kiloan' ? 'Laundry Kiloan (Pakaian Sehari-hari)' : 'Laundry Per Item / Satuan';

        if ($removedCount > 0) {
            $this->notify("Layanan diubah ke {$serviceLabel}. {$removedCount} item yang tidak sesuai telah disesuaikan.", "info");
        } else {
            $this->notify("Layanan diubah ke {$serviceLabel}.", "info");
        }
    }

    public function selectPackageType(string $package)
    {
        $this->package_type = $package;
        $label = $package === 'premium' ? 'Paket Premium ✨' : 'Paket Ekonomis';
        $this->notify("Paket laundry diubah ke {$label}.", "info");
    }

    public function selectSpeedType(string $speed)
    {
        $this->speed_type = $speed;
        $label = $speed === 'ekspres' ? '🚀 Ekspres (24 Jam)' : '⚡ Reguler (2 Hari)';
        $this->notify("Kecepatan pengerjaan diubah ke {$label}.", "info");
    }

    public function selectWashOption(string $option)
    {
        $this->wash_option = $option;
        $label = match ($option) {
            'cuci_saja' => 'Cuci Saja (Tanpa Lipat/Setrika)',
            'cuci_lipat' => 'Cuci & Lipat (Tanpa Setrika)',
            'setrika_saja' => 'Setrika Saja',
            default => 'Cuci & Setrika (Lengkap)',
        };
        $this->notify("Opsi laundry diubah ke {$label}.", "info");
    }

    public function updateQuantity($serviceId, $value)
    {
        $sId = (int) $serviceId;
        $val = max(0, (int) $value);
        $service = Service::find($sId);

        if ($service && $val > 0) {
            $isEligible = $this->service_type === 'kiloan' ? $service->isKiloanEligible() : $service->isSatuanEligible();
            if (!$isEligible) {
                $this->quantities[$sId] = 0;
                $targetServiceType = $this->service_type === 'kiloan' ? 'Per Item / Satuan' : 'Kiloan';
                $itemCategoryLabel = $this->service_type === 'kiloan' ? 'khusus Layanan Satuan' : 'khusus pakaian sehari-hari Layanan Kiloan';
                $this->notify("⚠️ Item '{$service->name}' {$itemCategoryLabel}! Silakan ubah ke Layanan {$targetServiceType} untuk memilih item ini.", "warning");
                return;
            }
        }

        $this->quantities[$sId] = $val;
        $this->step1_error = '';

        if ($service) {
            if (strtolower($service->name) === 'item lainnya') {
                $this->has_custom_item = $val > 0;
            }
            if ($val > 0) {
                $this->notify("✏️ Jumlah '{$service->name}' diubah menjadi {$val}.", "success");
            } else {
                $this->notify("🗑️ '{$service->name}' dihapus dari pesanan.", "info");
            }
        }
    }

    public function incrementQuantity($serviceId)
    {
        $sId = (int) $serviceId;
        $service = Service::find($sId);

        if (!$service) {
            return;
        }

        // Check item eligibility for current service type
        $isEligible = $this->service_type === 'kiloan' ? $service->isKiloanEligible() : $service->isSatuanEligible();

        if (!$isEligible) {
            $targetServiceType = $this->service_type === 'kiloan' ? 'Per Item / Satuan' : 'Kiloan';
            $itemCategoryLabel = $this->service_type === 'kiloan' ? 'khusus Layanan Satuan' : 'khusus pakaian sehari-hari Layanan Kiloan';
            $this->notify("⚠️ Item '{$service->name}' {$itemCategoryLabel}! Silakan ubah ke Layanan {$targetServiceType} untuk memilih item ini.", "warning");
            return;
        }

        $current = (int) ($this->quantities[$sId] ?? 0);
        $newQty = $current + 1;
        $this->quantities[$sId] = $newQty;
        $this->step1_error = '';

        if (strtolower($service->name) === 'item lainnya') {
            $this->has_custom_item = true;
        }

        $this->notify("➕ '{$service->name}' ditambahkan (Total: {$newQty}).", "success");
    }

    public function decrementQuantity($serviceId)
    {
        $sId = (int) $serviceId;
        $service = Service::find($sId);

        $current = (int) ($this->quantities[$sId] ?? 0);
        if ($current <= 0) {
            return;
        }

        $newQty = $current - 1;
        $this->quantities[$sId] = $newQty;

        if ($service && strtolower($service->name) === 'item lainnya' && $newQty <= 0) {
            $this->has_custom_item = false;
        }

        if ($newQty === 0 && $service) {
            $this->notify("🗑️ '{$service->name}' dihapus dari pesanan.", "info");
        } elseif ($service) {
            $this->notify("➖ '{$service->name}' dikurangi (Total: {$newQty}).", "info");
        }
    }

    public function updatedCustomItemName()
    {
        $this->notify("📝 Nama item lainnya diperbarui.", "info");
    }

    public function updatedCustomItemQty()
    {
        $this->notify("🔢 Jumlah item lainnya diubah menjadi {$this->custom_item_qty}.", "info");
    }

    public function updatedCustomItemNotes()
    {
        $this->notify("📝 Catatan item lainnya diperbarui.", "info");
    }

    public function goToStep2()
    {
        $selectedCount = 0;
        foreach ($this->quantities as $id => $qty) {
            $selectedCount += (int) $qty;
        }

        if ($selectedCount <= 0 && !$this->has_custom_item) {
            $this->step1_error = 'Pilih minimal 1 item laundry untuk melanjutkan.';
            $this->notify("⚠️ Pilih minimal 1 item laundry untuk melanjutkan.", "warning");
            session()->flash('step1_error', $this->step1_error);
            return;
        }

        if ($this->has_custom_item) {
            $this->validate([
                'custom_item_name' => 'required|string|max:255',
                'custom_item_qty' => 'required|integer|min:1',
            ]);
        }

        $this->step1_error = '';
        $this->step = 2;
        $this->notify("📍 Melanjutkan ke Step 2: Lokasi & Jadwal Penjemputan", "success");
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
            $days = $this->speed_type === 'ekspres' ? 1 : 2;
            $this->delivery_date = date('Y-m-d', strtotime($this->pickup_date . " +{$days} days"));
            $this->delivery_time = '14:00';
        }

        $this->step = 3;
        $this->notify("🚚 Melanjutkan ke Step 3: Jadwal & Alamat Pengantaran (Delivery)", "success");
    }

    public function submitOrder(OrderService $orderService, PricingService $pricingService)
    {
        if (!$this->sameAsPickup) {
            $this->validate([
                'delivery_name' => 'required|string|max:255',
                'delivery_phone' => 'required|string|max:20',
                'delivery_address' => 'required|string|max:500',
                'delivery_date' => 'required|date|after_or_equal:today',
                'delivery_time' => 'required|string',
                'delivery_notes' => 'nullable|string|max:500',
            ]);
        } else {
            $days = $this->speed_type === 'ekspres' ? 1 : 2;
            $this->delivery_date = date('Y-m-d', strtotime($this->pickup_date . " +{$days} days"));
        }

        $itemsData = [];
        $itemsBreakdown = [];

        $allServices = Service::where('is_active', true)->get()->keyBy('id');

        foreach ($this->quantities as $serviceId => $qty) {
            $quantity = (int) $qty;
            if ($quantity > 0) {
                $service = $allServices->get($serviceId);
                $itemsData[] = [
                    'service_id' => (int) $serviceId,
                    'service_name' => $service ? $service->name : 'Laundry Item',
                    'quantity' => $quantity,
                ];
                if ($service) {
                    $itemsBreakdown[] = [
                        'name' => $service->name,
                        'quantity' => $quantity,
                    ];
                }
            }
        }

        if ($this->has_custom_item && !empty($this->custom_item_name)) {
            $itemsData[] = [
                'service_id' => null,
                'service_name' => $this->custom_item_name,
                'quantity' => $this->custom_item_qty,
            ];
            $itemsBreakdown[] = [
                'name' => $this->custom_item_name,
                'quantity' => $this->custom_item_qty,
            ];
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
            'delivery_date' => $this->delivery_date,
            'delivery_time' => $this->delivery_time,
            'delivery_notes' => $this->delivery_notes,
        ];

        // Calculation of estimates
        $estimatedWeight = null;
        $estimatedPrice = 0.0;

        if ($this->service_type === 'kiloan') {
            $estimatedWeight = $pricingService->calculateTotalEstimatedWeight($itemsBreakdown);
            $pricePerKg = $pricingService->getKiloanPricePerKg($this->package_type, $this->speed_type, $this->wash_option);
            $billWeight = max(1.0, $estimatedWeight);
            $estimatedPrice = round($billWeight * $pricePerKg, 2);
        } else {
            // Per item calculation
            foreach ($itemsData as $it) {
                $service = isset($it['service_id']) ? $allServices->get($it['service_id']) : null;
                if ($service) {
                    $baseP = $this->speed_type === 'ekspres' && $service->express_price > 0 ? $service->express_price : $service->price;
                    $price = $pricingService->getPerItemUnitPrice($baseP, $this->package_type, $this->wash_option);
                } else {
                    $price = 0.0;
                }
                $estimatedPrice += $price * $it['quantity'];
            }
        }

        $extraOptions = [
            'service_type' => $this->service_type,
            'package_type' => $this->package_type,
            'speed_type' => $this->speed_type,
            'wash_option' => $this->wash_option,
            'estimated_weight' => $estimatedWeight,
            'estimated_price' => $estimatedPrice,
            'custom_item_name' => $this->has_custom_item ? $this->custom_item_name : null,
            'custom_item_qty' => $this->has_custom_item ? $this->custom_item_qty : null,
            'custom_item_notes' => $this->has_custom_item ? $this->custom_item_notes : null,
        ];

        $this->createdOrder = $orderService->createOrder(
            Auth::user(),
            $itemsData,
            $pickupData,
            $deliveryData,
            $extraOptions
        );

        session()->flash('message', 'Pesanan laundry berhasil dibuat! Driver kami akan segera melakukan penjemputan.');

        return redirect()->route('customer.tracking', ['order' => $this->createdOrder->id]);
    }

    public function render(PricingService $pricingService)
    {
        $allServices = Service::where('is_active', true)
            ->where('category', 'item')
            ->orderByRaw("CASE WHEN LOWER(name) = 'item lainnya' THEN 99 WHEN item_type = 'kiloan' THEN 1 WHEN item_type = 'both' THEN 2 WHEN item_type = 'satuan' THEN 3 ELSE 4 END")
            ->orderBy('id')
            ->get()
            ->filter(fn($service) => $this->service_type === 'kiloan' ? $service->isKiloanEligible() : $service->isSatuanEligible())
            ->values();

        $selectedItems = [];
        $itemsBreakdown = [];

        foreach ($allServices as $service) {
            $qty = (int) ($this->quantities[$service->id] ?? 0);
            if ($qty > 0) {
                $unitPrice = $this->speed_type === 'ekspres' && $service->express_price > 0 ? $service->express_price : $service->price;
                $subtotal = $unitPrice * $qty;
                $selectedItems[] = [
                    'service' => $service,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
                $itemsBreakdown[] = [
                    'name' => $service->name,
                    'quantity' => $qty,
                ];
            }
        }

        if ($this->has_custom_item && !empty($this->custom_item_name)) {
            $itemsBreakdown[] = [
                'name' => $this->custom_item_name,
                'quantity' => $this->custom_item_qty,
            ];
        }

        $estimatedWeight = 0.0;
        $estimatedPrice = 0.0;
        $pricePerKg = 0.0;

        if ($this->service_type === 'kiloan') {
            $estimatedWeight = $pricingService->calculateTotalEstimatedWeight($itemsBreakdown);
            $pricePerKg = $pricingService->getKiloanPricePerKg($this->package_type, $this->speed_type, $this->wash_option);
            $billWeight = max(1.0, $estimatedWeight);
            $estimatedPrice = round($billWeight * $pricePerKg, 2);
        } else {
            foreach ($selectedItems as $item) {
                $adjustedUnitPrice = $pricingService->getPerItemUnitPrice($item['unit_price'], $this->package_type, $this->wash_option);
                $estimatedPrice += $adjustedUnitPrice * $item['quantity'];
            }
        }

        return view('livewire.customer.create-order', [
            'services' => $allServices,
            'selectedItems' => $selectedItems,
            'estimatedWeight' => $estimatedWeight,
            'estimatedPrice' => $estimatedPrice,
            'pricePerKg' => $pricePerKg,
        ])->layout('components.layouts.app');
    }
}
