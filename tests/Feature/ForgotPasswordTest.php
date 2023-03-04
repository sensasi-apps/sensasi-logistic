<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Helper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use refreshDatabase;

    public function test_forgot_password_page_can_be_rendered()
    {
        $this->get('forgot-password')
            ->assertStatus(200);
    }

    public function test_forgot_password_page_can_not_be_rendered_when_authenticated()
    {
        $this->actingAs(Helper::createSuperman());

        $this->get('forgot-password')
            ->assertRedirect(RouteServiceProvider::HOME)
            ->assertStatus(302);
    }

    public function test_user_can_request_password_reset_link()
    {
        $user = Helper::createSuperman();

        $this->post('forgot-password', [
            'email' => $user->email,
        ])
            ->assertRedirect('login')
            ->assertStatus(302);
    }

    public function test_reset_password_form_can_be_rendered()
    {
        $user = Helper::createSuperman();

        $token = $this->app['auth.password.broker']->createToken($user);

        $this->get("reset-password/{$token}?email={$user->email}")
            ->assertStatus(200);
    }

    public function test_password_can_be_updated()
    {
        $user = Helper::createSuperman();
        $token = $this->app['auth.password.broker']->createToken($user);

        $this->post("reset-password", [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertStatus(302);
    }
}
