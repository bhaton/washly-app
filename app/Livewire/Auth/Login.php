<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function fillDemo(string $role)
    {
        if ($role === 'admin') {
            $this->email = 'admin@laundry.test';
            $this->password = 'password';
        } elseif ($role === 'driver') {
            $this->email = 'driver@laundry.test';
            $this->password = 'password';
        } elseif ($role === 'customer') {
            $this->email = 'customer@laundry.test';
            $this->password = 'password';
        }
    }

    public function loginAs(string $role)
    {
        $email = match($role) {
            'admin' => 'admin@laundry.test',
            'driver' => 'driver@laundry.test',
            'customer' => 'customer@laundry.test',
            default => 'customer@laundry.test',
        };

        $user = User::where('email', $email)->first();
        if ($user) {
            Auth::login($user, true);
            session()->regenerate();

            if ($user->hasRole('admin')) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->hasRole('driver')) {
                return redirect()->intended('/driver/dashboard');
            } else {
                return redirect()->intended('/customer/dashboard');
            }
        }
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            $user = Auth::user();
            if (!$user->is_active) {
                Auth::logout();
                $this->addError('email', 'Akun Anda sedang dinonaktifkan.');
                return;
            }

            if ($user->hasRole('admin')) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->hasRole('driver')) {
                return redirect()->intended('/driver/dashboard');
            } else {
                return redirect()->intended('/customer/dashboard');
            }
        }

        $this->addError('email', 'Email atau password yang Anda masukkan salah.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }
}
