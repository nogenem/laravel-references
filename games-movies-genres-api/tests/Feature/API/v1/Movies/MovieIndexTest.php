<?php

namespace Tests\Feature\API\v1\Movies;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class MovieIndexTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_get_a_list_of_movies(): void
    {
        $movies = Movie::factory()->count(5)->create();

        $response = $this->get(route('api.v1.movies.index'), $this->authorizationHeaders);

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($response['data']), 5);
        $response->assertJsonPath('data.0.name', $movies[0]['name']);
    }

    public function test_authenticated_users_can_control_how_many_movies_per_page_are_returned(): void
    {
        Movie::factory()->count(5)->create();
        $perPage = 2;

        $response = $this->get(route('api.v1.movies.index', ['per_page' => $perPage]), $this->authorizationHeaders);

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($response['data']), $perPage);
    }
}
