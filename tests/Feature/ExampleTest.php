<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Guests hitting the protected home page are redirected to the login screen.
     */
    public function test_guests_are_redirected_to_login_from_home(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }
}
