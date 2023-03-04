<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Helper;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered()
    {
        $response = $this->get('login');

        $response->assertStatus(200);
    }

    public function test_login_page_can_not_be_rendered_when_user_authenticated()
    {
        $this->actingAs(Helper::createSuperman());

        $this->get('login')
            ->assertRedirect(RouteServiceProvider::HOME)
            ->assertStatus(302);
    }

    public function test_user_can_login()
    {
        $user = Helper::createSuperman();
        $this->post('login', [
            'email' => $user->email,
            'password' => 'superman',
        ])
        ->assertRedirect(RouteServiceProvider::HOME)
        ->assertStatus(302);
    }

    public function test_google_oauth_can_be_rendered()
    {
        $response = $this->get('login/oauth/google')
            ->assertStatus(302);

        $this->assertStringContainsString('https://accounts.google.com/o/oauth2/auth', $response->getTargetUrl());
    }

    public function test_user_can_login_via_google_oauth()
    {
        // TODO: implement this test
    }
}
