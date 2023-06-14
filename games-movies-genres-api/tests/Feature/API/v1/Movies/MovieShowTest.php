<?php

namespace Tests\Feature\API\v1\Movies;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class MovieShowTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_get_a_movie_by_id(): void
    {
        $movie = Movie::factory()->create();

        $response = $this->get(
            route('api.v1.movies.show', ['movie' => $movie->id]),
            $this->authorizationHeaders
        );

        $response->assertStatus(200);
        $response->assertJsonPath('name', $movie['name']);
    }

    public function test_authenticated_users_cant_get_a_movie_with_invalid_id(): void
    {
        $movie = Movie::factory()->create();

        $response = $this->get(
            route('api.v1.movies.show', ['movie' => $movie->id + 1]),
            $this->authorizationHeaders
        );

        $response->assertStatus(404);
        $this->assertNotEmpty($response['message']);
    }
}
