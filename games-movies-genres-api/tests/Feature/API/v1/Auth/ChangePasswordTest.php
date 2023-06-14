<?php

namespace Tests\Feature\API\v1\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_change_their_password(): void
    {
        $currentPasswordPlain = 'password'; // Comes from the factory
        $currentPasswordHashed = $this->authorizedUser->password;
        $newPassword = 'new-password';

        $response = $this->post(route('api.v1.auth.change-password'), [
            'current_password' => $currentPasswordPlain,
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ], $this->authorizationHeaders);

        $response->assertStatus(200);
        $this->assertNotEmpty($response['message']);

        $user = $this->authorizedUser->fresh();
        $this->assertNotEquals($user->password, $currentPasswordHashed);
    }

    public function test_authenticated_users_cant_change_their_password_when_providing_a_wrong_current_password(): void
    {
        $currentPassword = 'password'; // Comes from the factory
        $currentPasswordHashed = $this->authorizedUser->password;
        $newPassword = 'new-password';

        $response = $this->post(route('api.v1.auth.change-password'), [
            'current_password' => $currentPassword . '_invalid',
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ], $this->authorizationHeaders);

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $user = $this->authorizedUser->fresh();
        $this->assertEquals($user->password, $currentPasswordHashed);
    }
}
