<?php

namespace Database\Seeders;

use App\Models\Genre;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    use TruncateTable;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncate('genres');

        Genre::factory()->count(50)->create();
    }
}
