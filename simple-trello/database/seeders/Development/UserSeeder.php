<?php

namespace Database\Seeders\Development;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\Traits\TruncateTable;

class UserSeeder extends Seeder
{
    use TruncateTable;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate("users");

        User::factory()->create([
            'name' => 'Test',
            'email' => 'test@test.com'
        ]);
        User::factory()->count(50)->create();
    }
}
