<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    /** @test */
    public function maintenance_mode_can_be_enabled_and_bypassed_with_secret(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.maintenance.on'))
            ->assertRedirect();

        $this->assertTrue(app()->isDownForMaintenance());

        $this->get('/')
            ->assertStatus(503);

        $secret = config('app.maintenance_secret');
        $this->get('/' . $secret)->assertRedirect('/');

        $this->get('/')->assertOk();

        $this->post(route('admin.maintenance.off'))->assertRedirect();
        $this->assertFalse(app()->isDownForMaintenance());
    }
}
