<?php

namespace Tests\Browser\Tests\Tasks;

use App\Models\Task;
use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TaskSortTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_users_can_sort_tasks_on_the_board()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();

        $status = Task::STATUSES['PENDING'];
        $tasks = [
            Task::factory()->create([
                'status' => $status,
                'priority' => Task::PRIORITIES['HIGH'],
                'deadline' => '2023-01-02 00:00:00'
            ]),
            Task::factory()->create([
                'status' => $status,
                'priority' => Task::PRIORITIES['MEDIUM'],
                'deadline' => '2023-01-01 00:00:00'
            ]),
        ];

        $this->browse(function (Browser $browser) use ($user, $status, $tasks) {
            $browser->loginAs($user)->visit('/tasks');

            $browser->with("@tasks-board-track-$status", function ($track) use ($tasks) {
                $track->assertSeeIn(".tasks-board-card:first-child", $tasks[0]->title)
                    ->assertSeeIn(".tasks-board-card:last-child", $tasks[1]->title);

                // $track->screenshot("users_can_sort_tasks_1");

                $track->click("@tasks-board-track-options-menu")
                    ->waitForText('Sort by...')
                    ->pressAndWaitFor('Deadline', 1)
                    ->assertSeeIn(".tasks-board-card:first-child", $tasks[1]->title)
                    ->assertSeeIn(".tasks-board-card:last-child", $tasks[0]->title);

                // $track->screenshot("users_can_sort_tasks_2");
            });
        });
    }
}
