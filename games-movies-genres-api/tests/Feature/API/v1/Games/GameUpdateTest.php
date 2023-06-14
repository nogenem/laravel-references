<?php

namespace Tests\Feature\API\v1\Games;

use App\Models\Game;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class GameUpdateTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_update_games(): void
    {
        $savedGenresNames = Genre::factory()->count(3)->create()->pluck('name')->toArray();
        $savedGame = Game::factory()
            ->hasGenres(1)
            ->create();

        $requestBody = $savedGame->toArray();
        $requestBody['genres'] = $savedGenresNames;
        $requestBody['name'] = $requestBody['name'] . ' Edited';

        $response = $this->patch(
            route('api.v1.games.update', ['game' => $savedGame->id]),
            $requestBody,
            $this->authorizationHeaders
        );

        $response->assertStatus(200);
        $response->assertJsonPath('name', $requestBody['name']);

        $game = $savedGame->fresh();
        $this->assertEquals($game->name, $requestBody['name']);

        $genresNames = $game->genres()->select('name')->get()->pluck('name')->toArray();
        $this->assertEquals($savedGenresNames, $genresNames);
    }

    public function test_authenticated_users_cant_save_games_with_an_already_existing_name(): void
    {
        $savedGame1 = Game::factory()->create();
        $savedGame2 = Game::factory()->create();

        $savedGame2Name = $savedGame2->name;

        $requestBody = $savedGame2->toArray();
        $requestBody['name'] = $savedGame1->name;

        $response = $this->patch(
            route('api.v1.games.update', ['game' => $savedGame2->id]),
            $requestBody,
            $this->authorizationHeaders
        );

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $game = $savedGame2->fresh();
        $this->assertEquals($game->name, $savedGame2Name);
    }

    public function test_authenticated_users_cant_update_a_game_with_invalid_id(): void
    {
        $savedGame = Game::factory()->create();

        $requestBody = $savedGame->toArray();
        $requestBody['name'] = $requestBody['name'] . ' Edited';

        $response = $this->patch(
            route('api.v1.games.update', ['game' => $savedGame->id + 1]),
            $requestBody,
            $this->authorizationHeaders
        );

        $response->assertStatus(404);
        $this->assertNotEmpty($response['message']);
    }

    public function test_authenticated_users_cant_update_games_with_invalid_genres(): void
    {
        $savedGenresNames = Genre::factory()->count(3)->create()->pluck('name')->toArray();
        $savedGenresNames[] = $savedGenresNames[0] . ' Invalid';

        $savedGame = Game::factory()
            ->hasGenres(1)
            ->create();

        $requestBody = $savedGame->toArray();
        $requestBody['genres'] = $savedGenresNames;
        $requestBody['name'] = $requestBody['name'] . ' Edited';

        $response = $this->patch(
            route('api.v1.games.update', ['game' => $savedGame->id]),
            $requestBody,
            $this->authorizationHeaders
        );

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $game = $savedGame->fresh();
        $gameGenresCount = $game->genres()->count();
        $this->assertNotEquals($game->name, $requestBody['name']);
        $this->assertEquals($gameGenresCount, 1);
    }
}
