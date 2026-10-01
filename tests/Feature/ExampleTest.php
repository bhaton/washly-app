<?php

namespace Tests\Feature;

use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(ServiceSeeder::class);
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
