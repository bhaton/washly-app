<?php

namespace App\Livewire\Admin\Drivers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingDriverId = null;

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $password = '';
    public bool $is_active = true;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['editingDriverId', 'name', 'email', 'phone', 'address', 'password']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(int $driverId)
    {
        $driver = User::findOrFail($driverId);
        $this->editingDriverId = $driver->id;
        $this->name = $driver->name;
        $this->email = $driver->email;
        $this->phone = $driver->phone ?? '';
        $this->address = $driver->address ?? '';
        $this->is_active = (bool) $driver->is_active;
        $this->password = '';
        $this->showModal = true;
    }

    public function saveDriver()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'is_active' => 'boolean',
        ];

        if ($this->editingDriverId) {
            $rules['email'] = 'required|email|unique:users,email,' . $this->editingDriverId;
            $rules['password'] = 'nullable|string|min:8';
        } else {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:8';
        }

        $this->validate($rules);

        if ($this->editingDriverId) {
            $driver = User::findOrFail($this->editingDriverId);
            $payload = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'is_active' => $this->is_active,
            ];
            if (!empty($this->password)) {
                $payload['password'] = Hash::make($this->password);
            }
            $driver->update($payload);
            session()->flash('message', "Data driver {$driver->name} berhasil diperbarui.");
        } else {
            $driver = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'password' => Hash::make($this->password),
                'is_active' => $this->is_active,
            ]);
            $driver->assignRole('driver');
            session()->flash('message', "Driver baru {$driver->name} berhasil ditambahkan.");
        }

        $this->showModal = false;
    }

    public function toggleStatus(int $driverId)
    {
        $driver = User::findOrFail($driverId);
        $driver->is_active = !$driver->is_active;
        $driver->save();

        $statusText = $driver->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('message', "Status driver {$driver->name} berhasil {$statusText}.");
    }

    public function render()
    {
        $drivers = User::role('driver')
            ->when(!empty($this->search), function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount(['pickupTasks', 'deliveryTasks'])
            ->latest()
            ->paginate(10);

        return view('livewire.admin.drivers.index', compact('drivers'))
            ->layout('components.layouts.app');
    }
}
