<?php

namespace Tests\Feature;

use App\Models\Promotion;
use App\Models\User;
use Database\Seeders\RoleAndUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PromotionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndUserSeeder::class);
    }

    public function test_admin_can_access_promotions_page_and_create_promotion()
    {
        $admin = User::role('admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.promotions.index'));
        $response->assertStatus(200);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Promotions\Index::class)
            ->set('title', 'Promo Diskon 50% Weekend')
            ->set('badge', 'DISKON 50%')
            ->set('description', 'Dapatkan diskon 50% khusus order hari Sabtu & Minggu.')
            ->set('promo_code', 'WEEKEND50')
            ->set('is_active', true)
            ->call('save');

        $this->assertDatabaseHas('promotions', [
            'title' => 'Promo Diskon 50% Weekend',
            'promo_code' => 'WEEKEND50',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_toggle_promotion_status()
    {
        $admin = User::role('admin')->first();
        $promo = Promotion::create([
            'title' => 'Promo Test Toggle',
            'description' => 'Test Deskripsi',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Promotions\Index::class)
            ->call('toggleActive', $promo->id);

        $this->assertDatabaseHas('promotions', [
            'id' => $promo->id,
            'is_active' => false,
        ]);
    }

    public function test_customer_dashboard_renders_active_promotion_popup()
    {
        $customer = User::role('customer')->first();
        $promo = Promotion::create([
            'title' => 'Promo Spesial Pelanggan Setia',
            'badge' => 'DISKON 30%',
            'description' => 'Potongan 30% untuk semua order laundry kiloan.',
            'promo_code' => 'SETIA30',
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Promo Spesial Pelanggan Setia');
        $response->assertSee('SETIA30');
    }

    public function test_admin_can_upload_promotion_poster_image()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $admin = User::role('admin')->first();
        $file = \Illuminate\Http\UploadedFile::fake()->image('poster.jpg');

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Promotions\Index::class)
            ->set('title', 'Promo Banner Poster')
            ->set('description', 'Deskripsi banner poster')
            ->set('imageUpload', $file)
            ->call('save');

        $promo = Promotion::where('title', 'Promo Banner Poster')->first();
        $this->assertNotNull($promo);
        $this->assertStringContainsString('promotions/', $promo->image);
    }
}
