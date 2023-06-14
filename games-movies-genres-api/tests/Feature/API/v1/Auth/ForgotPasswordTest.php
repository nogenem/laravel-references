<?php

namespace Tests\Feature\API\v1\Auth;

use App\Models\User;
use App\Notifications\API\v1\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_request_a_reset_password_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post(route('api.v1.auth.forgot-password'), [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
        $this->assertNotEmpty($response['message']);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_users_cant_request_a_reset_password_link_with_invalid_email(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post(route('api.v1.auth.forgot-password'), [
            'email' => $user->email . '.br',
        ]);

        $response->assertStatus(200);
        $this->assertNotEmpty($response['message']);

        Notification::assertNotSentTo($user, ResetPasswordNotification::class);
    }
}
