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
        'email' => 'required',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        $loginInput = trim($this->email);

        $user = User::where('email', $loginInput)
            ->orWhere('phone', $loginInput)
            ->first();

        if ($user && Auth::attempt(['id' => $user->id, 'password' => $this->password], $this->remember)) {
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

        $this->addError('email', 'Email / nomor telepon atau password yang Anda masukkan salah.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }
}
