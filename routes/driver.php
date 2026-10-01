<?php

use App\Livewire\Driver\Dashboard;
use App\Livewire\Driver\DeliveryTasks;
use App\Livewire\Driver\PickupTasks;
use App\Livewire\Driver\TaskDetail;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/pickups', PickupTasks::class)->name('pickups');
Route::get('/deliveries', DeliveryTasks::class)->name('deliveries');
Route::get('/tasks/{order}/{type?}', TaskDetail::class)->name('task-detail');
