<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected static function booted(): void
    {
        static::created(function (User $user) {
            $customerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer']);
            if (!$user->roles()->exists()) {
                $user->assignRole($customerRole);
            }
        });
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function customerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function pickupOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'pickup_driver_id');
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_driver_id');
    }

    public function pickupTasks(): HasMany
    {
        return $this->hasMany(Pickup::class, 'driver_id');
    }

    public function deliveryTasks(): HasMany
    {
        return $this->hasMany(Delivery::class, 'driver_id');
    }
}
