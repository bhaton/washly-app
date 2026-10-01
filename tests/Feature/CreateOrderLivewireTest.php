<?php

namespace Tests\Feature;

use App\Livewire\Customer\CreateOrder;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\RoleAndUserSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateOrderLivewireTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndUserSeeder::class);
        $this->seed(ServiceSeeder::class);
    }

    public function test_customer_can_increment_quantity_and_go_to_step_2()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();
        $service = Service::where('name', 'Kemeja')->first();

        Livewire::actingAs($customer)
            ->test(CreateOrder::class)
            ->assertSet('step', 1)
            ->call('incrementQuantity', $service->id)
            ->call('goToStep2')
            ->assertSet('step', 2);
    }

    public function test_cannot_proceed_to_step_2_without_selecting_items()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();

        Livewire::actingAs($customer)
            ->test(CreateOrder::class)
            ->assertSet('step', 1)
            ->call('goToStep2')
            ->assertSet('step', 1)
            ->assertSet('step1_error', 'Pilih minimal 1 item laundry (kuantitas > 0) dengan menekan tombol (+) untuk melanjutkan ke lokasi pickup.');
    }
}
