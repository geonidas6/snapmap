<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminResetTest extends TestCase
{
    public function test_guest_cannot_access_reset_route(): void
    {
        $response = $this->post('/admin/reset');

        $response->assertRedirect('/login');
    }
}
