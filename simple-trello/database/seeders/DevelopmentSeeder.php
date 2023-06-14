<?php

namespace Database\Seeders;

use Database\Seeders\Development\TaskSeeder;
use Database\Seeders\Development\UserSeeder;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Seeders to run during development, just to have some data for the UI
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            TaskSeeder::class,
        ]);
    }
}
