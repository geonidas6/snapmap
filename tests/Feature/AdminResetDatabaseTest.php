<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResetDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reset_database(): void
    {
        // Assume an "admin" user exists
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post('/admin/reset-database');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_reset_database(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/reset-database');

        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->post('/admin/reset-database');

        $response->assertRedirect('/login');
    }
}
