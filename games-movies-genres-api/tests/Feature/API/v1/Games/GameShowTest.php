<?php

namespace Tests\Feature\API\v1\Games;

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class GameShowTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_get_a_game_by_id(): void
    {
        $game = Game::factory()->create();

        $response = $this->get(
            route('api.v1.games.show', ['game' => $game->id]),
            $this->authorizationHeaders
        );

        $response->assertStatus(200);
        $response->assertJsonPath('name', $game['name']);
    }

    public function test_authenticated_users_cant_get_a_game_with_invalid_id(): void
    {
        $game = Game::factory()->create();

        $response = $this->get(
            route('api.v1.games.show', ['game' => $game->id + 1]),
            $this->authorizationHeaders
        );

        $response->assertStatus(404);
        $this->assertNotEmpty($response['message']);
    }
}
