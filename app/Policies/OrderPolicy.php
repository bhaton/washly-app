<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('customer')) {
            return $order->customer_id === $user->id;
        }

        if ($user->hasRole('driver')) {
            return $order->pickup_driver_id === $user->id || $order->delivery_driver_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('customer') || $user->hasRole('admin');
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('driver')) {
            return $order->pickup_driver_id === $user->id || $order->delivery_driver_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }
}
