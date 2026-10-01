<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $driverRole = Role::firstOrCreate(['name' => 'driver']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create Admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@laundry.test'],
            [
                'name' => 'Admin Outlet Washly',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'address' => 'Jl. Washly Outlet No. 1, Jakarta Central',
                'is_active' => true,
            ]
        );
        $admin->assignRole($adminRole);

        // Create Main Driver user
        $driver = User::updateOrCreate(
            ['email' => 'driver@laundry.test'],
            [
                'name' => 'Budi Driver Pickup',
                'password' => Hash::make('password'),
                'phone' => '081299887766',
                'address' => 'Jl. Express No. 45, Jakarta',
                'is_active' => true,
            ]
        );
        $driver->assignRole($driverRole);

        // Create Second Driver user (for testing separate pickup/delivery driver)
        $driver2 = User::updateOrCreate(
            ['email' => 'driver2@laundry.test'],
            [
                'name' => 'Siti Driver Delivery',
                'password' => Hash::make('password'),
                'phone' => '081233445566',
                'address' => 'Jl. Courier No. 88, Jakarta',
                'is_active' => true,
            ]
        );
        $driver2->assignRole($driverRole);

        // Create Main Customer user
        $customer = User::updateOrCreate(
            ['email' => 'customer@laundry.test'],
            [
                'name' => 'Ahmad Customer',
                'password' => Hash::make('password'),
                'phone' => '081511223344',
                'address' => 'Jl. Mawar Mekar No. 12, Jakarta',
                'is_active' => true,
            ]
        );
        $customer->assignRole($customerRole);
    }
}
