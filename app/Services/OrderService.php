<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        protected PricingService $pricingService,
        protected OrderStatusService $orderStatusService
    ) {}

    /**
     * Create a new order for a customer.
     */
    public function createOrder(
        User $customer,
        array $itemsData,
        array $pickupData,
        array $deliveryData,
        array $extraOptions = []
    ): Order {
        $serviceType = $extraOptions['service_type'] ?? 'kiloan';
        $packageType = $extraOptions['package_type'] ?? ($serviceType === 'kiloan' ? 'ekonomis' : null);
        $speedType = $extraOptions['speed_type'] ?? 'reguler';

        $orderItemsPayload = [];
        $pricingItemsPayload = [];

        foreach ($itemsData as $item) {
            $serviceId = $item['service_id'] ?? null;
            $quantity = (int) ($item['quantity'] ?? 0);
            $customUnitPrice = isset($item['unit_price']) ? (float) $item['unit_price'] : null;

            if ($quantity <= 0) {
                continue;
            }

            if ($serviceId) {
                $service = Service::find($serviceId);
                if ($service && !$service->is_active) {
                    throw new InvalidArgumentException("Layanan {$service->name} sedang tidak aktif.");
                }
                $serviceName = $service ? $service->name : ($item['service_name'] ?? 'Laundry Item');
                $unitPrice = $customUnitPrice ?? ($service ? (float) ($speedType === 'ekspres' && $service->express_price > 0 ? $service->express_price : $service->price) : 0.0);
            } else {
                $serviceName = $item['service_name'] ?? 'Item Laundry';
                $unitPrice = $customUnitPrice ?? 0.0;
            }

            $subtotal = round($unitPrice * $quantity, 2);

            $orderItemsPayload[] = [
                'service_id' => $serviceId,
                'service_name' => $serviceName,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];

            $pricingItemsPayload[] = [
                'price' => $unitPrice,
                'quantity' => $quantity,
            ];
        }

        $estimatedWeight = $extraOptions['estimated_weight'] ?? null;
        $estimatedPrice = $extraOptions['estimated_price'] ?? null;

        if ($serviceType === 'kiloan' && !empty($orderItemsPayload)) {
            $breakdown = array_map(fn($it) => ['name' => $it['service_name'], 'quantity' => $it['quantity']], $orderItemsPayload);
            if (!$estimatedWeight) {
                $estimatedWeight = $this->pricingService->calculateTotalEstimatedWeight($breakdown);
            }
            if (!$estimatedPrice) {
                $pricePerKg = $this->pricingService->getKiloanPricePerKg($packageType ?? 'ekonomis', $speedType);
                $billWeight = max(1.0, (float) $estimatedWeight);
                $estimatedPrice = round($billWeight * $pricePerKg, 2);
            }
        } elseif (empty($estimatedPrice)) {
            $totals = $this->pricingService->calculate($pricingItemsPayload);
            $estimatedPrice = $totals['total'];
        }

        $initialSubtotal = $estimatedPrice ?? 0.00;
        $initialTotal = $initialSubtotal;

        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_id' => $customer->id,
            'service_type' => $serviceType,
            'package_type' => $packageType,
            'speed_type' => $speedType,
            'wash_option' => $extraOptions['wash_option'] ?? 'cuci_setrika',
            'estimated_weight' => $estimatedWeight,
            'estimated_price' => $estimatedPrice,
            'custom_item_name' => $extraOptions['custom_item_name'] ?? null,
            'custom_item_qty' => $extraOptions['custom_item_qty'] ?? null,
            'custom_item_notes' => $extraOptions['custom_item_notes'] ?? null,
            'status' => 'MENUNGGU_PICKUP',
            'subtotal' => $initialSubtotal,
            'shipping_fee' => 0.00,
            'total' => $initialTotal,

            'pickup_name' => $pickupData['pickup_name'],
            'pickup_phone' => $pickupData['pickup_phone'],
            'pickup_address' => $pickupData['pickup_address'],
            'pickup_date' => $pickupData['pickup_date'],
            'pickup_time' => $pickupData['pickup_time'],
            'pickup_notes' => $pickupData['pickup_notes'] ?? null,

            'delivery_name' => $deliveryData['delivery_name'],
            'delivery_phone' => $deliveryData['delivery_phone'],
            'delivery_address' => $deliveryData['delivery_address'],
            'delivery_date' => $deliveryData['delivery_date'] ?? null,
            'delivery_time' => $deliveryData['delivery_time'] ?? null,
            'delivery_notes' => $deliveryData['delivery_notes'] ?? null,
        ]);

        foreach ($orderItemsPayload as $itemData) {
            $order->orderItems()->create($itemData);
        }

        $this->orderStatusService->transition($order, 'MENUNGGU_PICKUP', $customer, 'Pesanan berhasil dibuat oleh customer');

        return $order;
    }
}
