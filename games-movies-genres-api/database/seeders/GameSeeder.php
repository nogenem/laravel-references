<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Genre;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    use TruncateTable;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncate("games");

        $genres = Genre::all();
        if (count($genres) === 0) {
            $genres = Genre::factory()->count(10)->create();
        }

        $games = Game::factory()->count(50)->create();

        $games->each(function ($game) use ($genres) {
            $game->genres()->attach($genres->random(rand(2, 5))->pluck('id')->toArray());
        });
    }
}
