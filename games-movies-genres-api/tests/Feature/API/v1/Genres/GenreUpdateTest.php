<?php

namespace Tests\Feature\API\v1\Genres;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class GenreUpdateTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_update_genres(): void
    {
        $savedGenre = Genre::factory()->create();

        $requestBody = $savedGenre->toArray();
        $requestBody['name'] = $requestBody['name'] . ' Edited';

        $response = $this->patch(
            route('api.v1.genres.update', ['genre' => $savedGenre->id]),
            $requestBody,
            $this->authorizationHeaders
        );

        $response->assertStatus(200);
        $response->assertJsonPath('name', $requestBody['name']);

        $genre = $savedGenre->fresh();
        $this->assertEquals($genre->name, $requestBody['name']);
    }

    public function test_authenticated_users_cant_save_genres_with_an_already_existing_name(): void
    {
        $savedGenre1 = Genre::factory()->create();
        $savedGenre2 = Genre::factory()->create();

        $savedGenre2Name = $savedGenre2->name;

        $requestBody = $savedGenre2->toArray();
        $requestBody['name'] = $savedGenre1->name;

        $response = $this->patch(
            route('api.v1.genres.update', ['genre' => $savedGenre2->id]),
            $requestBody,
            $this->authorizationHeaders
        );

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $genre = $savedGenre2->fresh();
        $this->assertEquals($genre->name, $savedGenre2Name);
    }

    public function test_authenticated_users_cant_update_a_genre_with_invalid_id(): void
    {
        $savedGenre = Genre::factory()->create();

        $requestBody = $savedGenre->toArray();
        $requestBody['name'] = $requestBody['name'] . ' Edited';

        $response = $this->patch(
            route('api.v1.genres.update', ['genre' => $savedGenre->id + 1]),
            $requestBody,
            $this->authorizationHeaders
        );

        $response->assertStatus(404);
        $this->assertNotEmpty($response['message']);
    }
}
