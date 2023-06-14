<?php

namespace Tests\Feature\API\v1\Games;

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class GameDestroyTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_delete_games(): void
    {
        $game = Game::factory()->create();
        $gameId = $game->id;

        $response = $this->delete(
            route('api.v1.games.destroy', ['game' => $gameId]),
            [],
            $this->authorizationHeaders
        );

        $response->assertStatus(200);
        $this->assertNotEmpty($response['message']);

        $gameCount = Game::where('id', $gameId)->count();
        $this->assertEquals($gameCount, 0);
    }

    public function test_authenticated_users_cant_delete_a_game_with_invalid_id(): void
    {
        $game = Game::factory()->create();

        $response = $this->delete(
            route('api.v1.games.destroy', ['game' => $game->id + 1]),
            [],
            $this->authorizationHeaders
        );

        $response->assertStatus(404);
        $this->assertNotEmpty($response['message']);
    }
}
