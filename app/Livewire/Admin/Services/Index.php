<?php

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingServiceId = null;

    public string $name = '';
    public string $description = '';
    public string $price = '';
    public bool $is_active = true;

    public function openCreateModal()
    {
        $this->reset(['editingServiceId', 'name', 'description', 'price']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(int $serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $this->editingServiceId = $service->id;
        $this->name = $service->name;
        $this->description = $service->description ?? '';
        $this->price = (string) $service->price;
        $this->is_active = (bool) $service->is_active;
        $this->showModal = true;
    }

    public function saveService()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($this->editingServiceId) {
            $service = Service::findOrFail($this->editingServiceId);
            $service->update([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', "Layanan {$service->name} berhasil diperbarui.");
        } else {
            $service = Service::create([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', "Layanan baru {$service->name} berhasil ditambahkan.");
        }

        $this->showModal = false;
    }

    public function toggleStatus(int $serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $service->is_active = !$service->is_active;
        $service->save();

        $statusText = $service->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('message', "Status layanan {$service->name} berhasil {$statusText}.");
    }

    public function render()
    {
        $services = Service::when(!empty($this->search), function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.services.index', compact('services'))
            ->layout('components.layouts.app');
    }
}
