<?php

namespace App\Services;

class PricingService
{
    /**
     * Calculate order pricing from array of items: [['price' => float, 'quantity' => int], ...]
     */
    public function calculate(array $items): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $price = (float) ($item['price'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($quantity > 0) {
                $subtotal += $price * $quantity;
            }
        }

        $shippingFee = 0.00; // Shipping is strictly FREE / GRATIS per PRD Section 16
        $total = $subtotal + $shippingFee;

        return [
            'subtotal' => round($subtotal, 2),
            'shipping_fee' => $shippingFee,
            'total' => round($total, 2),
        ];
    }
}
