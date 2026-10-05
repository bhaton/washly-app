<?php

namespace App\Livewire\Admin\Admins;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingAdminId = null;

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $password = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['editingAdminId', 'name', 'email', 'phone', 'address', 'password']);
        $this->showModal = true;
    }

    public function openEditModal(int $adminId)
    {
        $admin = User::findOrFail($adminId);
        $this->editingAdminId = $admin->id;
        $this->name = $admin->name;
        $this->email = $admin->email;
        $this->phone = $admin->phone ?? '';
        $this->address = $admin->address ?? '';
        $this->password = '';
        $this->showModal = true;
    }

    public function saveAdmin()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ];

        if ($this->editingAdminId) {
            $rules['email'] = 'required|email|unique:users,email,' . $this->editingAdminId;
            $rules['password'] = 'nullable|string|min:8';
        } else {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:8';
        }

        $this->validate($rules);

        if ($this->editingAdminId) {
            $admin = User::findOrFail($this->editingAdminId);
            $payload = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
            ];
            if (!empty($this->password)) {
                $payload['password'] = Hash::make($this->password);
            }
            $admin->update($payload);
            session()->flash('message', "Data akun admin {$admin->name} berhasil diperbarui.");
        } else {
            $admin = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'password' => Hash::make($this->password),
                'is_active' => true,
            ]);
            $admin->assignRole('admin');
            session()->flash('message', "Akun admin baru {$admin->name} berhasil ditambahkan.");
        }

        $this->showModal = false;
    }

    public function deleteAdmin(int $adminId)
    {
        if (Auth::id() === $adminId) {
            session()->flash('error', 'Anda tidak dapat menghapus akun admin Anda sendiri yang sedang aktif.');
            return;
        }

        try {
            $admin = User::findOrFail($adminId);
            $adminName = $admin->name;
            $admin->delete();
            session()->flash('message', "Akun admin {$adminName} berhasil dihapus.");
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal menghapus akun admin: {$e->getMessage()}");
        }
    }

    public function render()
    {
        $admins = User::role('admin')
            ->when(!empty($this->search), function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.admins.index', compact('admins'))
            ->layout('components.layouts.app');
    }
}
