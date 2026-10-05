<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_create_driver_account()
    {
        $this->seed();

        $admin = User::role('admin')->first();
        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\Drivers\Index::class)
            ->set('name', 'Driver Baru Test')
            ->set('email', 'driverbarutest@laundry.test')
            ->set('phone', '081999888777')
            ->set('address', 'Jl Kebon Sirih No 10')
            ->set('password', 'password123')
            ->set('is_active', true)
            ->call('saveDriver')
            ->assertSee('Driver baru Driver Baru Test berhasil ditambahkan.');

        $this->assertTrue(User::where('email', 'driverbarutest@laundry.test')->exists());
    }

    public function test_admin_can_delete_driver_account()
    {
        $this->seed();

        $admin = User::role('admin')->first();
        $this->actingAs($admin);

        $driver = User::role('driver')->first();

        Livewire::test(\App\Livewire\Admin\Drivers\Index::class)
            ->call('deleteDriver', $driver->id)
            ->assertSee('berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $driver->id]);
    }

    public function test_admin_can_view_create_and_delete_admin_account()
    {
        $this->seed();

        $admin = User::role('admin')->first();
        $this->actingAs($admin);

        // Create Admin
        Livewire::test(\App\Livewire\Admin\Admins\Index::class)
            ->set('name', 'Admin Tambahan')
            ->set('email', 'admintambahan@laundry.test')
            ->set('phone', '081222333444')
            ->set('password', 'password123')
            ->call('saveAdmin')
            ->assertSee('Akun admin baru Admin Tambahan berhasil ditambahkan.');

        $newAdmin = User::where('email', 'admintambahan@laundry.test')->first();
        $this->assertNotNull($newAdmin);
        $this->assertTrue($newAdmin->hasRole('admin'));

        // Delete new Admin
        Livewire::test(\App\Livewire\Admin\Admins\Index::class)
            ->call('deleteAdmin', $newAdmin->id)
            ->assertSee('berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $newAdmin->id]);
    }

    public function test_admin_cannot_delete_own_account()
    {
        $this->seed();

        $admin = User::role('admin')->first();
        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\Admins\Index::class)
            ->call('deleteAdmin', $admin->id)
            ->assertSee('Anda tidak dapat menghapus akun admin Anda sendiri');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_public_registration_strictly_creates_customer_account()
    {
        $this->seed();

        Livewire::test(\App\Livewire\Auth\Register::class)
            ->set('name', 'Customer Baru Registered')
            ->set('email', 'customerbarureg@laundry.test')
            ->set('phone', '081234123412')
            ->set('address', 'Jl Sudirman No 5')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect('/customer/dashboard');

        $registeredUser = User::where('email', 'customerbarureg@laundry.test')->first();
        $this->assertNotNull($registeredUser);
        $this->assertTrue($registeredUser->hasRole('customer'));
        $this->assertFalse($registeredUser->hasRole('admin'));
        $this->assertFalse($registeredUser->hasRole('driver'));
    }
}

