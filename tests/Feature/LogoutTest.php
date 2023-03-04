<?php

namespace Tests\Feature;

use Helper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use refreshDatabase;

    public function test_user_can_be_logout_if_authenticated()
    {
        $this->actingAs(Helper::createSuperman());

        $this->post('logout')
            ->assertStatus(302);
    }

    public function test_user_can_not_be_logout_when_unauthenticated()
    {
        $this->post('logout')
            ->assertRedirect('login')
            ->assertStatus(302);
    }
}
