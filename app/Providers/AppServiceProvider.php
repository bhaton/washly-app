<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('roles')) {
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'driver']);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer']);
            }
        } catch (\Throwable $e) {
            // Ignore if DB not ready during migration
        }
    }
}
