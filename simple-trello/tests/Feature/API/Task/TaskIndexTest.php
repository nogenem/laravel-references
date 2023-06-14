<?php

namespace Tests\Feature\API\Task;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_get_a_list_of_tasks(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $tasks = Task::factory()->count(5)->create()->sortByDesc("deadline")->values();

        $response = $this->actingAs($user)->getJson(route('api.tasks.index'));

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($tasks), count($response['data']));
        $response->assertJsonPath('data.0.id', $tasks[0]['id']);
    }

    public function test_unauthenticated_users_cant_get_a_list_of_tasks(): void
    {
        Task::factory()->count(5)->create();

        $response = $this->getJson(route('api.tasks.index'));

        $response->assertStatus(401);
        $this->assertNotEmpty($response['message']);
    }

    public function test_users_can_filter_the_returned_list_of_tasks_by_status(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();

        $pendingStatus = Task::STATUSES['PENDING'];
        $pendingTasks = Task::factory()
            ->count(2)
            ->create(['status' => $pendingStatus])
            ->sortByDesc("deadline")
            ->values();

        $inProgressStatus = Task::STATUSES['IN_PROGRESS'];
        $inProgressTasks = Task::factory()
            ->count(3)
            ->create(['status' => $inProgressStatus])
            ->sortByDesc("deadline")
            ->values();


        // Status = PENDING
        $response = $this->actingAs($user)->getJson(route('api.tasks.index', ['status' => $pendingStatus]));

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($pendingTasks), count($response['data']));
        $response->assertJsonPath('data.0.id', $pendingTasks[0]['id']);

        // Status = IN_PROGRESS
        $response = $this->actingAs($user)->getJson(route('api.tasks.index', ['status' => $inProgressStatus]));

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($inProgressTasks), count($response['data']));
        $response->assertJsonPath('data.0.id', $inProgressTasks[0]['id']);
    }

    public function test_users_can_filter_the_returned_list_of_tasks_by_priority(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();

        $lowPriority = Task::PRIORITIES['LOW'];
        $lowPriorityTasks = Task::factory()
            ->count(2)
            ->create(['priority' => $lowPriority])
            ->sortByDesc("deadline")
            ->values();

        $highPriority = Task::PRIORITIES['HIGH'];
        $highPriorityTasks = Task::factory()
            ->count(3)
            ->create(['priority' => $highPriority])
            ->sortByDesc("deadline")
            ->values();


        // Priority = LOW
        $response = $this->actingAs($user)->getJson(route('api.tasks.index', ['priority' => $lowPriority]));

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($lowPriorityTasks), count($response['data']));
        $response->assertJsonPath('data.0.id', $lowPriorityTasks[0]['id']);

        // Priority = HIGH
        $response = $this->actingAs($user)->getJson(route('api.tasks.index', ['priority' => $highPriority]));

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $this->assertEquals(count($highPriorityTasks), count($response['data']));
        $response->assertJsonPath('data.0.id', $highPriorityTasks[0]['id']);
    }

    public function test_users_can_sort_the_returned_list_of_tasks(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $tasks = Task::factory()->count(10)->create();

        // Sort by Priority ASC
        $response = $this->actingAs($user)->getJson(route('api.tasks.index', ['sort' => 'priority']));

        $sortedTasks = $tasks->sortBy('priority')->values();

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $response->assertJsonPath('data.0.id', $sortedTasks[0]['id']);

        // Sort by Priority DESC
        $response = $this->actingAs($user)->getJson(route('api.tasks.index', ['sort' => '-priority']));

        $sortedTasks = $tasks->sortByDesc('priority')->values();

        $response->assertStatus(200);
        $this->assertIsArray($response['data']);
        $response->assertJsonPath('data.0.id', $sortedTasks[0]['id']);
    }
}
