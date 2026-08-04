<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Guests must be redirected to the sign-in page before reaching the dashboard.
     */
    public function test_guest_is_redirected_to_signin_from_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('signin'));
    }

    /**
     * The sign-in page must be publicly accessible.
     */
    public function test_signin_page_is_accessible_for_guests(): void
    {
        $response = $this->get('/signin');

        $response->assertOk();
    }
}
