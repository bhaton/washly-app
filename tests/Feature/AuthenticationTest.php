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

    public function test_livewire_fill_demo_populates_fields()
    {
        Livewire::test(Login::class)
            ->call('fillDemo', 'admin')
            ->assertSet('email', 'admin@laundry.test')
            ->assertSet('password', 'password');
    }

    public function test_livewire_login_as_logs_in_user_directly()
    {
        Livewire::test(Login::class)
            ->call('loginAs', 'customer')
            ->assertRedirect('/customer/dashboard');

        $this->assertAuthenticated();
    }
}
