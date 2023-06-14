<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Movie;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    use TruncateTable;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncate("movies");

        $genres = Genre::all();
        if (count($genres) === 0) {
            $genres = Genre::factory()->count(10)->create();
        }

        $movies = Movie::factory()->count(50)->create();

        $movies->each(function ($movie) use ($genres) {
            $movie->genres()->attach($genres->random(rand(2, 5))->pluck('id')->toArray());
        });
    }
}
