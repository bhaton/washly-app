<?php

use App\Livewire\Admin\Customers;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Drivers;
use App\Livewire\Admin\Orders;
use App\Livewire\Admin\Payments;
use App\Livewire\Admin\Reports;
use App\Livewire\Admin\Services;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/orders', Orders\Index::class)->name('orders.index');
Route::get('/orders/{order}', Orders\Show::class)->name('orders.show');
Route::get('/customers', Customers\Index::class)->name('customers.index');
Route::get('/drivers', Drivers\Index::class)->name('drivers.index');
Route::get('/services', Services\Index::class)->name('services.index');
Route::get('/payments', Payments\Index::class)->name('payments.index');
Route::get('/reports', Reports\Index::class)->name('reports.index');
