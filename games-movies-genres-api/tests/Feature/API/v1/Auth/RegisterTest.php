<?php

namespace Tests\Feature\API\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        $email = 'test@email.com';
        $response = $this->post(route('api.v1.auth.register'), [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('email', $email);

        $userCount = User::where('email', $email)->count();
        $this->assertEquals($userCount, 1);
    }
}
