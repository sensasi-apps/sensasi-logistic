<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Helper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InitializeAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_initialize_app_page_can_be_rendered()
    {
        $response = $this->get('initialize-app')
            ->assertRedirect('initialize-app/check')
            ->assertStatus(302);

        $this->get('initialize-app/check')
            ->assertRedirect('initialize-app/create-admin-user')
            ->assertStatus(302);

        $this->get('initialize-app/create-admin-user')
            ->assertStatus(200);
    }

    public function test_initialize_app_page_can_not_be_rendered_when_super_admin_is_exists()
    {
        Helper::createSuperman();

        $this->get('initialize-app')
            ->assertRedirect('initialize-app/check')
            ->assertStatus(302);

        $this->get('initialize-app/check')
            ->assertRedirect(RouteServiceProvider::HOME)
            ->assertStatus(302);

        $this->get('initialize-app/create-admin-user')
            ->assertStatus(403);

        $this->get(RouteServiceProvider::HOME)
            ->assertRedirect('login')
            ->assertStatus(302);

        $this->get('login')
            ->assertStatus(200);
    }

    public function test_super_admin_user_can_be_registered()
    {
        $user = [
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->post('initialize-app/create-admin-user', $user)
            ->assertRedirect('initialize-app/check')
            ->assertStatus(302);
        $apiToken = $response->getCookie('api-token')->getValue();

        $this->get('initialize-app/check')
            ->assertRedirect(RouteServiceProvider::HOME)
            ->assertStatus(302);

        $this->withCookie('api-token', $apiToken)
            ->get(RouteServiceProvider::HOME)
            ->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'email' => $user['email']
        ]);
    }

    public function test_google_oauth_can_be_rendered()
    {
        $response = $this->get('initialize-app/create-admin-user/oauth/google')
            ->assertStatus(302);

        $this->assertStringContainsString('https://accounts.google.com/o/oauth2/auth', $response->getTargetUrl());
    }

    public function test_can_handle_callback_oauth_google()
    {
        // TODO: Mock Socialite facade
    }
}
