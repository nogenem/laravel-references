<?php

namespace Tests\Feature\API\v1\Movies;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class MovieUpdateTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_update_movies(): void
    {
        $savedGenresNames = Genre::factory()->count(3)->create()->pluck('name')->toArray();
        $savedMovie = Movie::factory()
            ->hasGenres(1)
            ->create();

        $requestBody = $savedMovie->toArray();
        $requestBody['genres'] = $savedGenresNames;
        $requestBody['name'] = $requestBody['name'] . ' Edited';

        $response = $this->patch(
            route('api.v1.movies.update', ['movie' => $savedMovie->id]),
            $requestBody,
            $this->authorizationHeaders
        );

        $response->assertStatus(200);
        $response->assertJsonPath('name', $requestBody['name']);

        $movie = $savedMovie->fresh();
        $this->assertEquals($movie->name, $requestBody['name']);

        $genresNames = $movie->genres()->select('name')->get()->pluck('name')->toArray();
        $this->assertEquals($savedGenresNames, $genresNames);
    }

    public function test_authenticated_users_cant_save_movies_with_an_already_existing_name(): void
    {
        $savedMovie1 = Movie::factory()->create();
        $savedMovie2 = Movie::factory()->create();

        $savedMovie2Name = $savedMovie2->name;

        $requestBody = $savedMovie2->toArray();
        $requestBody['name'] = $savedMovie1->name;

        $response = $this->patch(
            route('api.v1.movies.update', ['movie' => $savedMovie2->id]),
            $requestBody,
            $this->authorizationHeaders
        );

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $movie = $savedMovie2->fresh();
        $this->assertEquals($movie->name, $savedMovie2Name);
    }

    public function test_authenticated_users_cant_update_a_movie_with_invalid_id(): void
    {
        $savedMovie = Movie::factory()->create();

        $requestBody = $savedMovie->toArray();
        $requestBody['name'] = $requestBody['name'] . ' Edited';

        $response = $this->patch(
            route('api.v1.movies.update', ['movie' => $savedMovie->id + 1]),
            $requestBody,
            $this->authorizationHeaders
        );

        $response->assertStatus(404);
        $this->assertNotEmpty($response['message']);
    }

    public function test_authenticated_users_cant_update_movies_with_invalid_genres(): void
    {
        $savedGenresNames = Genre::factory()->count(3)->create()->pluck('name')->toArray();
        $savedGenresNames[] = $savedGenresNames[0] . ' Invalid';

        $savedMovie = Movie::factory()
            ->hasGenres(1)
            ->create();

        $requestBody = $savedMovie->toArray();
        $requestBody['genres'] = $savedGenresNames;
        $requestBody['name'] = $requestBody['name'] . ' Edited';

        $response = $this->patch(
            route('api.v1.movies.update', ['movie' => $savedMovie->id]),
            $requestBody,
            $this->authorizationHeaders
        );

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $movie = $savedMovie->fresh();
        $movieGenresCount = $movie->genres()->count();
        $this->assertNotEquals($movie->name, $requestBody['name']);
        $this->assertEquals($movieGenresCount, 1);
    }
}
