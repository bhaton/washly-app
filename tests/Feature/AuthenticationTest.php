<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Models\User;
use Database\Seeders\RoleAndUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndUserSeeder::class);
    }

    public function test_admin_can_login_and_access_admin_dashboard()
    {
        $admin = User::where('email', 'admin@laundry.test')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_driver_can_login_and_access_driver_dashboard()
    {
        $driver = User::where('email', 'driver@laundry.test')->first();

        $response = $this->actingAs($driver)->get('/driver/dashboard');
        $response->assertStatus(200);
    }

    public function test_customer_can_login_and_access_customer_dashboard()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();

        $response = $this->actingAs($customer)->get('/customer/dashboard');
        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_admin_dashboard()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();

        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_driver_cannot_access_admin_dashboard()
    {
        $driver = User::where('email', 'driver@laundry.test')->first();

        $response = $this->actingAs($driver)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_orders_index_page()
    {
        $admin = User::where('email', 'admin@laundry.test')->first();

        $response = $this->actingAs($admin)->get('/admin/orders');
        $response->assertStatus(200);
    }

    public function test_user_can_login_via_livewire_form()
    {
        Livewire::test(Login::class)
            ->set('email', 'customer@laundry.test')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect('/customer/dashboard');

        $this->assertAuthenticated();
    }

    public function test_dev_role_switcher_route()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();
        $this->actingAs($customer);

        $response = $this->get('/dev/switch/admin');
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertEquals('admin@laundry.test', auth()->user()->email);
    }
}
