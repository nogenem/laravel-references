<?php

namespace Tests\Feature\API\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_logout(): void
    {
        $response = $this->post(route('api.v1.auth.logout'), [], $this->authorizationHeaders);

        $response->assertStatus(200);
        $this->assertNotEmpty($response['message']);
    }

    public function test_unauthenticated_users_cant_logout(): void
    {
        User::factory()->create();

        $response = $this->post(route('api.v1.auth.logout'));

        $response->assertStatus(401);
        $this->assertNotEmpty($response['message']);
    }

    public function test_logged_out_users_cant_access_protected_route(): void
    {
        // Can access protected route
        $response = $this->get(route('api.v1.auth.user'), $this->authorizationHeaders);
        $response->assertStatus(200);

        // Log out
        $response = $this->post(route('api.v1.auth.logout'), [], $this->authorizationHeaders);
        $response->assertStatus(200);

        // Cant access protected route
        $response = $this->get(route('api.v1.auth.user'), $this->authorizationHeaders);

        $response->assertStatus(401);
        $this->assertNotEmpty($response['message']);
    }
}
