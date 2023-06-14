<?php

namespace Tests\Feature\API\User;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_get_a_list_of_users(): void
    {
        $users = User::factory()->count(5)->create()->sortBy("name")->values();
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = $users[0];

        $response = $this->actingAs($user)->getJson(route('api.users.search'));

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($users), count($response['data']));
        $response->assertJsonPath('data.0.id', $users[0]['id']);
    }

    public function test_unauthenticated_users_cant_get_a_list_of_users(): void
    {
        User::factory()->count(5)->create()->sortBy("name")->values();

        $response = $this->getJson(route('api.users.search'));

        $response->assertStatus(401);
        $this->assertNotEmpty($response['message']);
    }

    public function test_users_can_filter_the_returned_list_of_users_by_name(): void
    {
        $janes = User::factory()->count(3)->create(['name' => 'Jane Doe'])->values();
        $johns = User::factory()->count(2)->create(['name' => 'John Doe'])->values();
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = $janes[0];

        // Name = jane
        $response = $this->actingAs($user)->getJson(route('api.users.search', ['name' => 'jane']));

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($janes), count($response['data']));
        $response->assertJsonPath('data.0.id', $janes[0]['id']);

        // Name = john
        $response = $this->actingAs($user)->getJson(route('api.users.search', ['name' => 'john']));

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($johns), count($response['data']));
        $response->assertJsonPath('data.0.id', $johns[0]['id']);
    }

    public function test_users_can_sort_the_returned_list_of_users(): void
    {
        $users = User::factory()->count(10)->create();
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = $users[0];

        // Sort by name ASC
        $response = $this->actingAs($user)->getJson(route('api.users.search', ['sort' => 'name']));

        $sortedUsers = $users->sortBy('name')->values();

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $response->assertJsonPath('data.0.id', $sortedUsers[0]['id']);

        // Sort by name DESC
        $response = $this->actingAs($user)->getJson(route('api.users.search', ['sort' => '-name']));

        $sortedUsers = $users->sortByDesc('name')->values();

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $response->assertJsonPath('data.0.id', $sortedUsers[0]['id']);
    }

    public function test_users_can_paginate_the_returned_list_of_users(): void
    {
        $users = User::factory()->count(5)->create()->sortBy("name")->values();
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = $users[0];

        $perPage = 2;

        // Page = 1
        $page = 1;

        $response = $this->actingAs($user)->getJson(
            route('api.users.search', ['page' => $page, 'per_page' => $perPage])
        );

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals($perPage, count($response['data']));
        $response->assertJsonPath('data.0.id', $users[0]['id']);

        // Page = 2
        $page = 2;

        $response = $this->actingAs($user)->getJson(
            route('api.users.search', ['page' => $page, 'per_page' => $perPage])
        );

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals($perPage, count($response['data']));
        // Should return the 3* user
        $response->assertJsonPath('data.0.id', $users[2]['id']);
    }
}
