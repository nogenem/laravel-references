<?php

namespace Tests\Browser\Tests\Tasks;

use App\Models\Task;
use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TaskDisplayTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_users_can_see_tasks_cards_on_the_board()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();

        $tasks = [
            Task::factory()->create([
                'status' => Task::STATUSES['PENDING'],
            ]),
            Task::factory()->create([
                'status' => Task::STATUSES['IN_PROGRESS'],
            ]),
            Task::factory()->create([
                'status' => Task::STATUSES['CONCLUDED'],
            ])
        ];

        $this->browse(function (Browser $browser) use ($user, $tasks) {
            $browser->loginAs($user)->visit('/tasks')
                ->assertSeeIn('@tasks-board-track-' . $tasks[0]->status, $tasks[0]->title)
                ->assertSeeIn('@tasks-board-track-' . $tasks[1]->status, $tasks[1]->title)
                ->assertSeeIn('@tasks-board-track-' . $tasks[2]->status, $tasks[2]->title);

            // $browser->screenshot("users_can_see_tasks");
        });
    }

    public function test_users_can_see_task_modal_after_clicking_on_a_card()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $task = Task::factory()->create();

        $this->browse(function (Browser $browser) use ($user, $task) {
            $browser->loginAs($user)->visit('/tasks')
                ->waitForText($task->title)
                ->click('@tasks-board-card-' . $task->id);

            $browser->whenAvailable('@modal', function ($modal) use ($task) {
                $modal->assertSee($task->title)
                    ->assertSee($task->status)
                    ->assertSee($task->priority)
                    ->assertSee($task->createdBy->name);

                // $modal->screenshot("users_can_see_task_modal");
            }, 2);
        });
    }

    public function test_users_can_see_task_modal_when_selectedTaskId_query_param_is_set()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $task = Task::factory()->create();

        $this->browse(function (Browser $browser) use ($user, $task) {
            $browser->loginAs($user)->visit('/tasks?selectedTaskId=' . $task->id);

            $browser->whenAvailable('@modal', function ($modal) use ($task) {
                $modal->assertSee($task->title)
                    ->assertSee($task->status)
                    ->assertSee($task->priority)
                    ->assertSee($task->createdBy->name);

                // $modal->screenshot("users_can_see_task_modal_when_query_param_is_set");
            }, 2);
        });
    }
}
