<?php

namespace Database\Seeders\Development;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    use TruncateTable;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate("tasks");

        $usersCount = User::count();
        if ($usersCount == 0) {
            User::factory()->count(10)->create();
        }

        Task::factory()->count(50)->create();
    }
}
