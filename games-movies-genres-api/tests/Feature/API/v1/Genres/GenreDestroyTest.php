<?php

namespace Tests\Feature\API\v1\Genres;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class GenreDestroyTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_delete_genres(): void
    {
        $genre = Genre::factory()->create();
        $genreId = $genre->id;

        $response = $this->delete(
            route('api.v1.genres.destroy', ['genre' => $genreId]),
            [],
            $this->authorizationHeaders
        );

        $response->assertStatus(200);
        $this->assertNotEmpty($response['message']);

        $genreCount = Genre::where('id', $genreId)->count();
        $this->assertEquals($genreCount, 0);
    }

    public function test_authenticated_users_cant_delete_a_genre_with_invalid_id(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->delete(
            route('api.v1.genres.destroy', ['genre' => $genre->id + 1]),
            [],
            $this->authorizationHeaders
        );

        $response->assertStatus(404);
        $this->assertNotEmpty($response['message']);
    }
}
