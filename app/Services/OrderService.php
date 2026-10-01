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
     * $items format: [ ['service_id' => 1, 'quantity' => 2], ... ]
     */
    public function createOrder(User $customer, array $itemsData, array $pickupData, array $deliveryData): Order
    {
        if (empty($itemsData)) {
            throw new InvalidArgumentException('Minimal harus memilih 1 item laundry.');
        }

        // Load services and build item calculations
        $orderItemsPayload = [];
        $pricingItemsPayload = [];

        foreach ($itemsData as $item) {
            $serviceId = $item['service_id'] ?? null;
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($quantity <= 0) {
                continue;
            }

            $service = Service::findOrFail($serviceId);
            if (!$service->is_active) {
                throw new InvalidArgumentException("Layanan {$service->name} sedang tidak aktif.");
            }

            $unitPrice = (float) $service->price; // Price snapshot!
            $subtotal = round($unitPrice * $quantity, 2);

            $orderItemsPayload[] = [
                'service_id' => $service->id,
                'service_name' => $service->name,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];

            $pricingItemsPayload[] = [
                'price' => $unitPrice,
                'quantity' => $quantity,
            ];
        }

        if (empty($orderItemsPayload)) {
            throw new InvalidArgumentException('Jumlah item laundry harus lebih dari 0.');
        }

        $totals = $this->pricingService->calculate($pricingItemsPayload);

        // Generate unique order number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_id' => $customer->id,
            'status' => 'PENDING_PAYMENT',
            'subtotal' => $totals['subtotal'],
            'shipping_fee' => $totals['shipping_fee'],
            'total' => $totals['total'],
            
            'pickup_name' => $pickupData['pickup_name'],
            'pickup_phone' => $pickupData['pickup_phone'],
            'pickup_address' => $pickupData['pickup_address'],
            'pickup_date' => $pickupData['pickup_date'],
            'pickup_time' => $pickupData['pickup_time'],
            'pickup_notes' => $pickupData['pickup_notes'] ?? null,

            'delivery_name' => $deliveryData['delivery_name'],
            'delivery_phone' => $deliveryData['delivery_phone'],
            'delivery_address' => $deliveryData['delivery_address'],
            'delivery_notes' => $deliveryData['delivery_notes'] ?? null,
        ]);

        foreach ($orderItemsPayload as $itemData) {
            $order->orderItems()->create($itemData);
        }

        // Log history
        $this->orderStatusService->transition($order, 'PENDING_PAYMENT', $customer, 'Pesanan berhasil dibuat');

        return $order;
    }
}
