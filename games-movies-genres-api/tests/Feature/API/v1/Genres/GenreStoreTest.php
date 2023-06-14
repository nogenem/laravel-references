<?php

namespace Tests\Feature\API\v1\Genres;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class GenreStoreTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_store_genres(): void
    {
        $genre = Genre::factory()->make()->toArray();

        $response = $this->post(route('api.v1.genres.store'), $genre, $this->authorizationHeaders);

        $response->assertStatus(201);
        $response->assertJsonPath('name', $response['name']);

        $genreCount = Genre::where('name', $genre['name'])->count();
        $this->assertEquals($genreCount, 1);
    }

    public function test_authenticated_users_cant_store_genres_with_the_same_name(): void
    {
        $genre = Genre::factory()->make()->toArray();

        // Save genre
        $response = $this->post(route('api.v1.genres.store'), $genre, $this->authorizationHeaders);
        $response->assertStatus(201);

        // Try to save same genre
        $response = $this->post(route('api.v1.genres.store'), $genre, $this->authorizationHeaders);

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $genreCount = Genre::where('name', $genre['name'])->count();
        $this->assertEquals($genreCount, 1);
    }
}
