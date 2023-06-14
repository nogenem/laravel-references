<?php

namespace Tests\Browser\Tests\Tasks;

use App\Models\Task;
use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TaskEditTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_users_can_edit_tasks()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user1 = User::factory()->createOne();
        $user2 = User::factory()->createOne();
        $task = Task::factory()->create([
            'title' => 'Some title',
            'description' => 'Some description',
            'status' => Task::STATUSES['PENDING'],
            'priority' => Task::PRIORITIES['LOW'],
            'deadline' => '2023-01-01 00:00:00',
            'created_by' => $user1->id,
            'assigned_to' => null
        ]);

        $this->browse(function (Browser $browser) use ($user1, $user2, $task) {
            $updateValues = [
                'title' => 'Some new title',
                'description' => 'Some new description',
                'status' => Task::STATUSES['IN_PROGRESS'],
                'priority' => Task::PRIORITIES['HIGH'],
                'deadline' => [
                    'date' => '2024-02-02 01:01:00',
                    'keys' => ['02', '02', '2024', '{tab}', '01', '01']
                ],
                'assignedTo' => $user2->name,
            ];

            $browser->loginAs($user1)->visit('/tasks?selectedTaskId=' . $task->id);

            $browser->whenAvailable('@modal', function ($modal) use ($updateValues, $user2) {
                $modal->press('EDIT')
                    ->type('title', $updateValues['title'])
                    ->type('description', $updateValues['description'])
                    ->select('status', $updateValues['status'])
                    ->select('priority', $updateValues['priority'])
                    ->keys('input[name="deadline"]', ...$updateValues['deadline']['keys'])
                    ->click('input[name="assigned_to"]')
                    ->waitForText($user2->name)
                    ->press($user2->name);

                // $modal->screenshot("users_can_edit_tasks_1");

                $modal->pressAndWaitFor('SAVE')
                    ->waitUntilMissing('input[name="title"]')
                    ->assertSee($updateValues['title'])
                    ->assertSee($updateValues['description'])
                    ->assertSee($updateValues['status'])
                    ->assertSee($updateValues['priority'])
                    ->assertSee($updateValues['deadline']['keys'][2])
                    ->assertSee($user2->name);

                // $modal->screenshot("users_can_edit_tasks_2");
            }, 2);

            $updatedTask = $task->fresh();
            $this->assertEquals($updatedTask->title, $updateValues['title']);
            $this->assertEquals($updatedTask->description, $updateValues['description']);
            $this->assertEquals($updatedTask->status, $updateValues['status']);
            $this->assertEquals($updatedTask->priority, $updateValues['priority']);
            $this->assertEquals($updatedTask->deadline, $updateValues['deadline']['date']);
            $this->assertEquals($updatedTask->assigned_to, $user2->id);
        });
    }
}
