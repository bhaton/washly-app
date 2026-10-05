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
            ->assertSet('step1_error', 'Pilih minimal 1 item laundry untuk melanjutkan.');
    }

    public function test_only_kiloan_eligible_items_are_rendered_in_kiloan_mode()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();

        $test = Livewire::actingAs($customer)
            ->test(CreateOrder::class)
            ->assertSet('service_type', 'kiloan');

        $services = $test->viewData('services');

        $this->assertTrue($services->contains('name', 'Kaos'));
        $this->assertTrue($services->contains('name', 'Kemeja'));
        $this->assertTrue($services->contains('name', 'Jaket'));
        $this->assertFalse($services->contains('name', 'Jas'));
        $this->assertFalse($services->contains('name', 'Bed Cover'));
        $this->assertFalse($services->contains('name', 'Karpet'));
    }

    public function test_daily_wear_and_unit_items_are_rendered_in_satuan_mode()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();

        $test = Livewire::actingAs($customer)
            ->test(CreateOrder::class)
            ->call('selectServiceType', 'per_item')
            ->assertSet('service_type', 'per_item');

        $services = $test->viewData('services');

        $this->assertTrue($services->contains('name', 'Jas'));
        $this->assertTrue($services->contains('name', 'Sepatu'));
        $this->assertTrue($services->contains('name', 'Bed Cover'));
        $this->assertTrue($services->contains('name', 'Kaos'));
        $this->assertTrue($services->contains('name', 'Kemeja'));
    }

    public function test_customer_can_select_wash_option_and_receives_notification()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();

        Livewire::actingAs($customer)
            ->test(CreateOrder::class)
            ->assertSet('wash_option', 'cuci_setrika')
            ->call('selectWashOption', 'cuci_saja')
            ->assertSet('wash_option', 'cuci_saja')
            ->assertSet('notification.type', 'info')
            ->assertSee('Cuci Saja')
            ->call('selectWashOption', 'cuci_lipat')
            ->assertSet('wash_option', 'cuci_lipat')
            ->assertSet('notification.type', 'info')
            ->assertSee('Cuci & Lipat')
            ->call('selectWashOption', 'setrika_saja')
            ->assertSet('wash_option', 'setrika_saja')
            ->assertSet('notification.type', 'info')
            ->assertSee('Setrika Saja');
    }

    public function test_customer_cannot_add_satuan_item_in_kiloan_mode_and_receives_warning_notification()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();
        $jasService = Service::where('name', 'Jas')->first();

        Livewire::actingAs($customer)
            ->test(CreateOrder::class)
            ->assertSet('service_type', 'kiloan')
            ->call('incrementQuantity', $jasService->id)
            ->assertSet('quantities.' . $jasService->id, 0)
            ->assertSet('notification.type', 'warning');
    }

    public function test_switching_to_kiloan_mode_resets_incompatible_satuan_items_and_notifies_customer()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();
        $jasService = Service::where('name', 'Jas')->first();

        Livewire::actingAs($customer)
            ->test(CreateOrder::class)
            ->call('selectServiceType', 'per_item')
            ->call('incrementQuantity', $jasService->id)
            ->assertSet('quantities.' . $jasService->id, 1)
            ->call('selectServiceType', 'kiloan')
            ->assertSet('quantities.' . $jasService->id, 0)
            ->assertSet('notification.type', 'info')
            ->assertSee('item yang tidak sesuai telah disesuaikan');
    }

    public function test_notifications_are_triggered_for_every_update_action()
    {
        $customer = User::where('email', 'customer@laundry.test')->first();
        $kemejaService = Service::where('name', 'Kemeja')->first();

        Livewire::actingAs($customer)
            ->test(CreateOrder::class)
            ->call('selectPackageType', 'premium')
            ->assertSet('notification.type', 'info')
            ->assertSee('Paket Premium')
            ->call('selectSpeedType', 'ekspres')
            ->assertSet('notification.type', 'info')
            ->assertSee('Ekspres')
            ->call('incrementQuantity', $kemejaService->id)
            ->assertSet('notification.type', 'success')
            ->assertSee('Kemeja')
            ->call('decrementQuantity', $kemejaService->id)
            ->assertSet('notification.type', 'info')
            ->assertSee('dihapus');
    }
}
