<?php

namespace Tests\Feature\API\v1\Games;

use App\Models\Game;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasApiTokenAuthorizationHeaders;
use Tests\TestCase;

class GameStoreTest extends TestCase
{
    use RefreshDatabase, HasApiTokenAuthorizationHeaders;

    public function test_authenticated_users_can_store_games(): void
    {
        $genres = Genre::factory()->count(3)->create()->pluck('name')->toArray();
        $game = Game::factory()
            ->make()
            ->toArray();
        $game['genres'] = $genres;

        $response = $this->post(route('api.v1.games.store'), $game, $this->authorizationHeaders);

        $response->assertStatus(201);
        $response->assertJsonPath('name', $response['name']);

        $gameCount = Game::where('name', $game['name'])->count();
        $this->assertEquals($gameCount, 1);
    }

    public function test_authenticated_users_cant_store_games_with_the_same_name(): void
    {
        $genres = Genre::factory()->count(3)->create()->pluck('name')->toArray();
        $game = Game::factory()
            ->make()
            ->toArray();
        $game['genres'] = $genres;

        // Save game
        $response = $this->post(route('api.v1.games.store'), $game, $this->authorizationHeaders);
        $response->assertStatus(201);

        // Try to save same game
        $response = $this->post(route('api.v1.games.store'), $game, $this->authorizationHeaders);

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $gameCount = Game::where('name', $game['name'])->count();
        $this->assertEquals($gameCount, 1);
    }

    public function test_authenticated_users_cant_store_games_with_invalid_genres(): void
    {
        $genres = Genre::factory()->count(3)->create()->pluck('name')->toArray();
        $genres[] = $genres[0] . ' Invalid';

        $game = Game::factory()
            ->make()
            ->toArray();
        $game['genres'] = $genres;

        $response = $this->post(route('api.v1.games.store'), $game, $this->authorizationHeaders);

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $gameCount = Game::where('name', $game['name'])->count();
        $this->assertEquals($gameCount, 0);
    }
}
