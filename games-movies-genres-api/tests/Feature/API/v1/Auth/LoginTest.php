<?php

namespace Tests\Feature\API\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_login(): void
    {
        $user = User::factory()->create();
        $password = 'password'; // Comes from the factory

        $response = $this->post(route('api.v1.auth.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);
        $this->assertNotEmpty($response['token']);
    }

    public function test_users_cant_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create();
        $password = 'password'; // Comes from the factory

        // Wrong password
        $response = $this->post(route('api.v1.auth.login'), [
            'email' => $user->email,
            'password' => $password . '2',
        ]);

        $response->assertStatus(401);
        $this->assertNotEmpty($response['message']);

        // Wrong email
        $response = $this->post(route('api.v1.auth.login'), [
            'email' => $user->email . '.br',
            'password' => $password,
        ]);

        $response->assertStatus(401);
        $this->assertNotEmpty($response['message']);
    }
}
