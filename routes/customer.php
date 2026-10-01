<?php

use App\Livewire\Customer\CreateOrder;
use App\Livewire\Customer\Dashboard;
use App\Livewire\Customer\OrderDetail;
use App\Livewire\Customer\Orders;
use App\Livewire\Customer\Tracking;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/orders', Orders::class)->name('orders.index');
Route::get('/orders/create', CreateOrder::class)->name('orders.create');
Route::get('/orders/{order}', OrderDetail::class)->name('orders.show');
Route::get('/tracking/{order}', Tracking::class)->name('tracking');
