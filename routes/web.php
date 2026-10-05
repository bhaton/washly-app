<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Profile\Index as ProfileIndex;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if (!$user->roles()->exists()) {
            $customerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer']);
            $user->assignRole($customerRole);
            $user->load('roles');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('driver')) {
            return redirect()->route('driver.dashboard');
        } else {
            return redirect()->route('customer.dashboard');
        }
    }

    $services = Service::where('is_active', true)->get();
    return view('welcome', compact('services'));
})->name('home');

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout.get');

Route::get('/login', Login::class)->name('login');

// Special role login URLs automatically logout previous session to allow easy role switching
Route::get('/admin/login', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('admin.login');

Route::get('/driver/login', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('driver.login');

Route::get('/customer/login', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('customer.login');

Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    Route::get('/profile', ProfileIndex::class)->name('profile');
    Route::get('/admin/promotions', \App\Livewire\Admin\Promotions\Index::class)->name('admin.promotions.index')->middleware('role:admin');
    Route::get('/admin/reports/pdf', [\App\Http\Controllers\Admin\ReportPdfController::class, 'exportPdf'])->name('admin.reports.pdf')->middleware('role:admin');
});

// Midtrans Webhook / Notification Callback Route
Route::post('/api/midtrans/notification', [\App\Http\Controllers\MidtransWebhookController::class, 'handle'])->name('midtrans.notification');
Route::post('/midtrans/notification', [\App\Http\Controllers\MidtransWebhookController::class, 'handle']);

// Quick Dev Role Switcher Route for Easy Multi-Role Testing
Route::get('/dev/switch/{role}', function ($role) {
    if (!in_array($role, ['admin', 'driver', 'customer'])) {
        abort(404);
    }

    $email = "{$role}@laundry.test";
    $roleObj = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);

    $user = \App\Models\User::where('email', $email)->first();

    if (!$user) {
        $user = \App\Models\User::create([
            'name' => ucfirst($role) . ' Outlet Washly',
            'email' => $email,
            'password' => 'password',
            'phone' => '081234567890',
            'address' => 'Jl. Washly Outlet No. 1, Jakarta Central',
            'is_active' => true,
        ]);
        $user->assignRole($roleObj);
    } else {
        if (!$user->hasRole($role)) {
            $user->assignRole($roleObj);
        }
    }

    Auth::login($user);
    session()->regenerate();

    $targetRoute = match($role) {
        'admin' => 'admin.dashboard',
        'driver' => 'driver.dashboard',
        'customer' => 'customer.dashboard',
    };

    return redirect()->route($targetRoute)->with('message', "Berhasil beralih ke akun " . ucfirst($role) . " ({$user->email})");
})->name('dev.switch-role');

