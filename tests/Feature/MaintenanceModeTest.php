<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_enable_maintenance_and_get_secret_link(): void
    {
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->withHeader('Accept', 'application/json')
            ->post('/admin/maintenance/enable');

        $response->assertStatus(200);
        $response->assertJsonStructure(['secret']);
    }

    public function test_admin_can_disable_maintenance(): void
    {
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withHeader('Accept', 'application/json')
            ->post('/admin/maintenance/enable');

        $response = $this->actingAs($admin)
            ->withHeader('Accept', 'application/json')
            ->post('/admin/maintenance/disable');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_enable_maintenance(): void
    {
        $response = $this->post('/admin/maintenance/enable');

        $response->assertRedirect('/login');
    }
}
