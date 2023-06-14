<?php

namespace Tests\Feature\API\v1\Genres;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class GenreShowTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_get_a_genre_by_id(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->get(
            route('api.v1.genres.show', ['genre' => $genre->id]),
            $this->authorizationHeaders
        );

        $response->assertStatus(200);
        $response->assertJsonPath('name', $genre['name']);
    }

    public function test_authenticated_users_cant_get_a_genre_with_invalid_id(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->get(
            route('api.v1.genres.show', ['genre' => $genre->id + 1]),
            $this->authorizationHeaders
        );

        $response->assertStatus(404);
        $this->assertNotEmpty($response['message']);
    }
}
