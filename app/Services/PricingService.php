<?php

namespace App\Services;

class PricingService
{
    /**
     * Item weight reference (in kg per piece)
     */
    public const ITEM_WEIGHTS = [
        'kaos' => 0.20,
        'kemeja' => 0.25,
        'celana pendek' => 0.25,
        'celana panjang' => 0.50,
        'jeans' => 0.80,
        'jaket' => 1.00,
        'hoodie' => 0.80,
        'selimut' => 2.50,
        'bed cover' => 3.25,
        'default' => 0.30,
    ];

    /**
     * Kiloan base prices
     */
    public const KILOAN_PRICES = [
        'ekonomis' => [
            'reguler' => 8000.00,
            'ekspres' => 13000.00,
        ],
        'premium' => [
            'reguler' => 12000.00,
            'ekspres' => 18000.00,
        ],
    ];

    /**
     * Calculate item weight based on item name and quantity
     */
    public function getEstimatedWeightForItem(string $itemName, int $quantity): float
    {
        $nameLower = strtolower(trim($itemName));
        $unitWeight = self::ITEM_WEIGHTS['default'];

        foreach (self::ITEM_WEIGHTS as $key => $weight) {
            if ($key !== 'default' && str_contains($nameLower, $key)) {
                $unitWeight = $weight;
                break;
            }
        }

        return round($unitWeight * max(0, $quantity), 2);
    }

    /**
     * Calculate total estimated weight for kiloan order items breakdown
     * $itemsBreakdown: [ ['name' => 'Kaos', 'quantity' => 3], ... ]
     */
    public function calculateTotalEstimatedWeight(array $itemsBreakdown): float
    {
        $totalWeight = 0.0;
        foreach ($itemsBreakdown as $item) {
            $name = $item['name'] ?? '';
            $qty = (int) ($item['quantity'] ?? 0);
            $totalWeight += $this->getEstimatedWeightForItem($name, $qty);
        }

        return round($totalWeight, 2);
    }

    /**
     * Get price per kg for Kiloan with wash option rate adjustment
     */
    public function getKiloanPricePerKg(string $packageType, string $speedType, string $washOption = 'cuci_setrika'): float
    {
        $pkg = strtolower($packageType) === 'premium' ? 'premium' : 'ekonomis';
        $spd = strtolower($speedType) === 'ekspres' ? 'ekspres' : 'reguler';

        $basePrice = self::KILOAN_PRICES[$pkg][$spd] ?? 8000.00;

        if ($washOption === 'cuci_saja') {
            return max(1000.00, $basePrice - 1500.00);
        } elseif ($washOption === 'cuci_lipat') {
            return max(1000.00, $basePrice - 1000.00);
        } elseif ($washOption === 'setrika_saja') {
            return max(1000.00, $basePrice - 2000.00);
        }

        return $basePrice;
    }

    /**
     * Get per item unit price with package type and wash option rate adjustments
     */
    public function getPerItemUnitPrice(float $basePrice, string $packageType = 'ekonomis', string $washOption = 'cuci_setrika'): float
    {
        if ($basePrice <= 0) {
            return 0.0;
        }

        $price = $basePrice;

        // Premium Package Adjustment (+25% rounded to nearest 500)
        if (strtolower($packageType) === 'premium') {
            $price = ceil(($price * 1.25) / 500) * 500;
        }

        // Wash Option Adjustment
        if ($washOption === 'cuci_saja') {
            $price = round($price * 0.85, 2);
        } elseif ($washOption === 'cuci_lipat') {
            $price = round($price * 0.90, 2);
        } elseif ($washOption === 'setrika_saja') {
            $price = round($price * 0.70, 2);
        }

        return $price;
    }

    /**
     * Calculate order pricing from array of items: [['price' => float, 'quantity' => int], ...]
     */
    public function calculate(array $items): array
    {
        $subtotal = 0.0;
        foreach ($items as $item) {
            $price = (float) ($item['price'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($quantity > 0) {
                $subtotal += $price * $quantity;
            }
        }

        $shippingFee = 0.00;
        $total = $subtotal + $shippingFee;

        return [
            'subtotal' => round($subtotal, 2),
            'shipping_fee' => $shippingFee,
            'total' => round($total, 2),
        ];
    }
}
