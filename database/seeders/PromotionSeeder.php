<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        Promotion::updateOrCreate(
            ['title' => 'Promo Spesial Diskon 25% Semua Layanan'],
            [
                'badge' => 'DISKON 25%',
                'description' => 'Gunakan kode kupon WASHLY25 untuk mendapatkan potongan 25% untuk layanan Laundry Kiloan & Per Item. Nikmati cuci bersih, harum, dan higienis dengan antar jemput gratis!',
                'image' => 'images/logo-welcome.png',
                'promo_code' => 'WASHLY25',
                'is_active' => true,
            ]
        );
    }
}
