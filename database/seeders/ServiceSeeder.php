<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Kemeja',
                'description' => 'Layanan cuci kemeja formal & kasual + setrika halus.',
                'price' => 8000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Kaos',
                'description' => 'Layanan cuci kaos t-shirt / polo + lipat rapi.',
                'price' => 6000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Celana',
                'description' => 'Layanan cuci celana bahan / chino + setrika rapi.',
                'price' => 10000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Jeans',
                'description' => 'Layanan cuci khusus jeans / denim + wangi tahan lama.',
                'price' => 12000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Jaket',
                'description' => 'Layanan cuci jaket / sweater / hoodie anti bau apek.',
                'price' => 15000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Selimut',
                'description' => 'Layanan cuci selimut & sprei lembut higienis.',
                'price' => 20000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Sepatu',
                'description' => 'Layanan cuci & perawatan sepatu sneaker / formal.',
                'price' => 25000.00,
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
