<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:500',
        'password' => 'required|string|min:8|confirmed',
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'password' => $this->password,
            'is_active' => true,
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer']);
        $user->assignRole('customer');

        Auth::login($user);

        return redirect()->intended('/customer/dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('components.layouts.guest');
    }
}
