<?php

namespace Tests\Browser\Tests\Tasks;

use App\Models\Task;
use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TaskCreateTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_users_can_create_tasks()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();

        $this->browse(function (Browser $browser) use ($user) {
            $title = "Some title";

            $browser->loginAs($user)->visit('/tasks')
                ->click('@tasks-board-add-card-' . Task::STATUSES['PENDING']);

            // $browser->screenshot("users_can_create_tasks_1");

            $browser->whenAvailable("@editable-tasks-board-card", function ($card) use ($title) {
                $card->type('title', $title)
                    ->keys('input[name="title"]', '{enter}');
            }, 2);

            $browser->whenAvailable('@tasks-board-card-1', function ($card) use ($title) {
                $card->assertSee($title);

                // $card->screenshot("users_can_create_tasks_2");
            }, 2);

            $createdTask = Task::where('title', $title)->first();
            $this->assertNotNull($createdTask);
        });
    }
}
