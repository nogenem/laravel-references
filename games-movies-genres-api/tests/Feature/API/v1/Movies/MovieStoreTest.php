<?php

namespace Tests\Feature\API\v1\Movies;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class MovieStoreTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_store_movies(): void
    {
        $genres = Genre::factory()->count(3)->create()->pluck('name')->toArray();
        $movie = Movie::factory()
            ->make()
            ->toArray();
        $movie['genres'] = $genres;

        $response = $this->post(route('api.v1.movies.store'), $movie, $this->authorizationHeaders);

        $response->assertStatus(201);
        $response->assertJsonPath('name', $response['name']);

        $movieCount = Movie::where('name', $movie['name'])->count();
        $this->assertEquals($movieCount, 1);
    }

    public function test_authenticated_users_cant_store_movies_with_the_same_name(): void
    {
        $genres = Genre::factory()->count(3)->create()->pluck('name')->toArray();
        $movie = Movie::factory()
            ->make()
            ->toArray();
        $movie['genres'] = $genres;

        // Save movie
        $response = $this->post(route('api.v1.movies.store'), $movie, $this->authorizationHeaders);
        $response->assertStatus(201);

        // Try to save same movie
        $response = $this->post(route('api.v1.movies.store'), $movie, $this->authorizationHeaders);

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $movieCount = Movie::where('name', $movie['name'])->count();
        $this->assertEquals($movieCount, 1);
    }

    public function test_authenticated_users_cant_store_movies_with_invalid_genres(): void
    {
        $genres = Genre::factory()->count(3)->create()->pluck('name')->toArray();
        $genres[] = $genres[0] . ' Invalid';

        $movie = Movie::factory()
            ->make()
            ->toArray();
        $movie['genres'] = $genres;

        $response = $this->post(route('api.v1.movies.store'), $movie, $this->authorizationHeaders);

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $movieCount = Movie::where('name', $movie['name'])->count();
        $this->assertEquals($movieCount, 0);
    }
}
