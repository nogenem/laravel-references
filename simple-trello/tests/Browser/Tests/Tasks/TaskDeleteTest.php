<?php

namespace Tests\Browser\Tests\Tasks;

use App\Models\Task;
use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TaskDeleteTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_users_can_delete_tasks()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $task = Task::factory()->create();

        $this->browse(function (Browser $browser) use ($user, $task) {
            $browser->loginAs($user)->visit('/tasks?selectedTaskId=' . $task->id);

            // $browser->screenshot("users_can_delete_tasks_1");

            $browser->whenAvailable('@modal', function ($modal) {
                $modal->press('DELETE');
            }, 2);

            $browser->acceptDialog()
                ->waitUntilMissing('@modal')
                ->assertDontSee($task->title);

            // $browser->screenshot("users_can_delete_tasks_2");

            $task = Task::where('id', $task->id)->first();
            $this->assertNull($task);
        });
    }
}
