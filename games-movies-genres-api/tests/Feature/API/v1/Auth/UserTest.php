<?php

namespace Tests\Feature\API\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_get_their_information(): void
    {
        $response = $this->get(route('api.v1.auth.user'), $this->authorizationHeaders);

        $response->assertStatus(200);
        $response->assertJsonPath('email', $response['email']);
    }

    public function test_unauthenticated_users_cant_get_their_information(): void
    {
        User::factory()->create();

        $response = $this->get(route('api.v1.auth.user'));

        $response->assertStatus(401);
        $this->assertNotEmpty($response['message']);
    }
}
