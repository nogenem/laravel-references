<?php

namespace Tests\Feature\API\v1\Movies;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class MovieDestroyTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_delete_movies(): void
    {
        $movie = Movie::factory()->create();
        $movieId = $movie->id;

        $response = $this->delete(
            route('api.v1.movies.destroy', ['movie' => $movieId]),
            [],
            $this->authorizationHeaders
        );

        $response->assertStatus(200);
        $this->assertNotEmpty($response['message']);

        $movieCount = Movie::where('id', $movieId)->count();
        $this->assertEquals($movieCount, 0);
    }

    public function test_authenticated_users_cant_delete_a_movie_with_invalid_id(): void
    {
        $movie = Movie::factory()->create();

        $response = $this->delete(
            route('api.v1.movies.destroy', ['movie' => $movie->id + 1]),
            [],
            $this->authorizationHeaders
        );

        $response->assertStatus(404);
        $this->assertNotEmpty($response['message']);
    }
}
