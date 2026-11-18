<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guests_are_sent_to_login_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }
}
