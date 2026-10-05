<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Kiloan Package Options
            [
                'name' => 'Laundry Kiloan Ekonomis',
                'category' => 'kiloan',
                'item_type' => 'kiloan',
                'package_type' => 'ekonomis',
                'description' => 'Cuci kiloan murah & hemat + detergent standar + lipat rapi.',
                'price' => 8000.00,
                'express_price' => 13000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Laundry Kiloan Premium',
                'category' => 'kiloan',
                'item_type' => 'kiloan',
                'package_type' => 'premium',
                'description' => 'Cuci kiloan premium + softener harum tahan lama + setrika halus & disinfektan.',
                'price' => 12000.00,
                'express_price' => 18000.00,
                'is_active' => true,
            ],

            // Daily Clothes Items (Bisa Kiloan & Satuan)
            [
                'name' => 'Kaos',
                'category' => 'item',
                'item_type' => 'both',
                'package_type' => null,
                'description' => 'Item kaos t-shirt / polo (bisa kiloan / Rp6.000 per pcs).',
                'price' => 6000.00,
                'express_price' => 9000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Kemeja',
                'category' => 'item',
                'item_type' => 'both',
                'package_type' => null,
                'description' => 'Item kemeja formal / casual (bisa kiloan / Rp8.000 per pcs).',
                'price' => 8000.00,
                'express_price' => 12000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Celana',
                'category' => 'item',
                'item_type' => 'both',
                'package_type' => null,
                'description' => 'Item celana bahan / chino / pendek (bisa kiloan / Rp10.000 per pcs).',
                'price' => 10000.00,
                'express_price' => 15000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Jeans',
                'category' => 'item',
                'item_type' => 'both',
                'package_type' => null,
                'description' => 'Item celana / jaket jeans denim (bisa kiloan / Rp12.000 per pcs).',
                'price' => 12000.00,
                'express_price' => 17000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Rok',
                'category' => 'item',
                'item_type' => 'both',
                'package_type' => null,
                'description' => 'Item rok pendek / panjang (bisa kiloan / Rp8.000 per pcs).',
                'price' => 8000.00,
                'express_price' => 12000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Jaket',
                'category' => 'item',
                'item_type' => 'both',
                'package_type' => null,
                'description' => 'Layanan cuci jaket / sweater / parka (bisa kiloan / Rp25.000 per pcs).',
                'price' => 25000.00,
                'express_price' => 35000.00,
                'is_active' => true,
            ],

            // Special Unit Items (Hanya Satuan)
            [
                'name' => 'Jas',
                'category' => 'item',
                'item_type' => 'satuan',
                'package_type' => null,
                'description' => 'Layanan cuci jas formal & blazer + dry clean & gantungan.',
                'price' => 35000.00,
                'express_price' => 50000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Sepatu',
                'category' => 'item',
                'item_type' => 'satuan',
                'package_type' => null,
                'description' => 'Layanan cuci deep clean & perawatan khusus sepatu.',
                'price' => 40000.00,
                'express_price' => 55000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Selimut',
                'category' => 'item',
                'item_type' => 'satuan',
                'package_type' => null,
                'description' => 'Layanan cuci selimut tebal & sprei lembut higienis.',
                'price' => 35000.00,
                'express_price' => 50000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Bed Cover',
                'category' => 'item',
                'item_type' => 'satuan',
                'package_type' => null,
                'description' => 'Layanan cuci bed cover tebal + disinfektan & wangi segar.',
                'price' => 45000.00,
                'express_price' => 65000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Karpet',
                'category' => 'item',
                'item_type' => 'satuan',
                'package_type' => null,
                'description' => 'Layanan cuci karpet rumahan / tempat ibadah / kantor.',
                'price' => 50000.00,
                'express_price' => 75000.00,
                'is_active' => true,
            ],

            // Custom Item (Paling Bawah)
            [
                'name' => 'Item Lainnya',
                'category' => 'item',
                'item_type' => 'both',
                'package_type' => null,
                'description' => 'Layanan cuci barang custom / spesifik lainnya (harga dikonfirmasi admin).',
                'price' => 0.00,
                'express_price' => 0.00,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}
