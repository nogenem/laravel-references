<?php

namespace Tests\Feature\API\v1\Auth;

use App\Models\User;
use App\Notifications\API\v1\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_reset_password_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post(route('api.v1.auth.forgot-password'), [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function (object $notification) use ($user) {
            $currentPasswordHashed = $user->password;

            $response = $this->post(route('api.v1.auth.reset-password', [
                'email' => $user->email,
                'token' => $notification->token,
            ]), [
                'password' => 'password2',
                'password_confirmation' => 'password2',
            ]);

            $response->assertStatus(200);
            $this->assertNotEmpty($response['message']);

            $user = $user->fresh();
            $this->assertNotEquals($user->password, $currentPasswordHashed);

            return true;
        });
    }

    public function test_users_cant_reset_password_with_invalid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post(route('api.v1.auth.forgot-password'), [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function (object $notification) use ($user) {
            $currentPasswordHashed = $user->password;

            $response = $this->post(route('api.v1.auth.reset-password', [
                'email' => $user->email,
                'token' => $notification->token . '_invalid',
            ]), [
                'password' => 'password2',
                'password_confirmation' => 'password2',
            ]);

            $response->assertStatus(422);
            $this->assertNotEmpty($response['message']);

            $user = $user->fresh();
            $this->assertEquals($user->password, $currentPasswordHashed);

            return true;
        });
    }
}
