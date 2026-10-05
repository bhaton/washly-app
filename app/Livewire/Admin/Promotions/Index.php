<?php

namespace App\Livewire\Admin\Promotions;

use App\Models\Promotion;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $showModal = false;
    public $editingId = null;

    public $title = '';
    public $badge = '';
    public $description = '';
    public $promo_code = '';
    public $imageUpload;
    public $existingImage = '';
    public $is_active = true;

    protected $rules = [
        'title' => 'required|string|max:255',
        'badge' => 'nullable|string|max:100',
        'description' => 'required|string',
        'promo_code' => 'nullable|string|max:50',
        'imageUpload' => 'nullable|image|max:2048',
        'is_active' => 'boolean',
    ];

    public function create()
    {
        $this->reset(['editingId', 'title', 'badge', 'description', 'promo_code', 'imageUpload', 'existingImage', 'is_active']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(Promotion $promotion)
    {
        $this->editingId = $promotion->id;
        $this->title = $promotion->title;
        $this->badge = $promotion->badge;
        $this->description = $promotion->description;
        $this->promo_code = $promotion->promo_code;
        $this->existingImage = $promotion->image ?? '';
        $this->imageUpload = null;
        $this->is_active = (bool) $promotion->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $imagePath = $this->existingImage;
        if (empty($imagePath)) {
            $imagePath = 'images/logo-welcome.png';
        }

        if ($this->imageUpload) {
            $storedPath = $this->imageUpload->store('promotions', 'public');
            $imagePath = 'storage/' . $storedPath;
        }

        Promotion::updateOrCreate(
            ['id' => $this->editingId],
            [
                'title' => $this->title,
                'badge' => $this->badge,
                'description' => $this->description,
                'promo_code' => $this->promo_code,
                'image' => $imagePath,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('message', $this->editingId ? 'Promosi berhasil diperbarui!' : 'Promosi baru berhasil ditambahkan!');
        $this->showModal = false;
        $this->reset(['editingId', 'title', 'badge', 'description', 'promo_code', 'imageUpload', 'existingImage']);
    }

    public function toggleActive(Promotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);
        session()->flash('message', 'Status promosi berhasil diubah.');
    }

    public function delete(Promotion $promotion)
    {
        $promotion->delete();
        session()->flash('message', 'Promosi telah dihapus.');
    }

    public function render()
    {
        $promotions = Promotion::latest()->get();
        return view('livewire.admin.promotions.index', compact('promotions'))
            ->layout('components.layouts.app');
    }
}
