<?php

namespace App\Livewire\Admin\Payments;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public bool $showSettingsModal = false;

    // Midtrans Settings
    public string $serverKey = '';
    public string $clientKey = '';
    public bool $isProduction = false;

    public function mount()
    {
        $this->serverKey = config('midtrans.server_key', env('MIDTRANS_SERVER_KEY', ''));
        $this->clientKey = config('midtrans.client_key', env('MIDTRANS_CLIENT_KEY', ''));
        $this->isProduction = (bool) config('midtrans.is_production', false);
    }

    public function openSettingsModal()
    {
        $this->showSettingsModal = true;
    }

    public function closeSettingsModal()
    {
        $this->showSettingsModal = false;
    }

    public function saveSettings()
    {
        $this->validate([
            'serverKey' => 'required|string|max:255',
            'clientKey' => 'required|string|max:255',
        ]);

        try {
            $this->updateEnv([
                'MIDTRANS_SERVER_KEY' => $this->serverKey,
                'MIDTRANS_CLIENT_KEY' => $this->clientKey,
                'MIDTRANS_IS_PRODUCTION' => $this->isProduction,
            ]);

            config([
                'midtrans.server_key' => $this->serverKey,
                'midtrans.client_key' => $this->clientKey,
                'midtrans.is_production' => $this->isProduction,
            ]);

            $this->showSettingsModal = false;
            session()->flash('message', 'Pengaturan Midtrans Payment Gateway berhasil diperbarui & diaktifkan!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menyimpan pengaturan Midtrans: ' . $e->getMessage());
        }
    }

    protected function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return;
        }

        $content = file_get_contents($envPath);
        foreach ($data as $key => $value) {
            $formattedValue = is_bool($value) ? ($value ? 'true' : 'false') : '"' . trim($value, '"\'') . '"';
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$formattedValue}", $content);
            } else {
                $content .= "\n{$key}={$formattedValue}";
            }
        }

        file_put_contents($envPath, $content);
    }

    public function render()
    {
        $payments = Payment::with(['order.customer'])
            ->when(!empty($this->search), function ($q) {
                $q->where('payment_reference', 'like', '%' . $this->search . '%')
                  ->orWhereHas('order', function ($oq) {
                      $oq->where('order_number', 'like', '%' . $this->search . '%');
                  });
            })
            ->when(!empty($this->statusFilter), function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.payments.index', compact('payments'))
            ->layout('components.layouts.app');
    }
}
