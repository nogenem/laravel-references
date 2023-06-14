<?php

namespace Tests\Feature\API\v1\Games;

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class GameIndexTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_get_a_list_of_games(): void
    {
        $games = Game::factory()->count(5)->create();

        $response = $this->get(route('api.v1.games.index'), $this->authorizationHeaders);

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($response['data']), 5);
        $response->assertJsonPath('data.0.name', $games[0]['name']);
    }

    public function test_authenticated_users_can_control_how_many_games_per_page_are_returned(): void
    {
        Game::factory()->count(5)->create();
        $perPage = 2;

        $response = $this->get(route('api.v1.games.index', ['per_page' => $perPage]), $this->authorizationHeaders);

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($response['data']), $perPage);
    }
}
