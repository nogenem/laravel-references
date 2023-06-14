<?php

namespace Tests\Feature\API\Task;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_get_a_task_by_id(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $task = Task::factory()->createOne();

        $response = $this->actingAs($user)->getJson(route('api.tasks.show', ['task' => $task->id]));

        $response->assertStatus(200);
        $response->assertJsonPath('title', $task->title);
        $response->assertJsonPath('status', $task->status);
        $response->assertJsonPath('priority', $task->priority);
    }

    public function test_unauthenticated_users_cant_get_a_task_by_id(): void
    {
        $task = Task::factory()->createOne();

        $response = $this->getJson(route('api.tasks.show', ['task' => $task->id]));

        $response->assertStatus(401);
        $this->assertNotEmpty($response['message']);
    }

    public function test_users_cant_get_a_task_with_invalid_id(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $task = Task::factory()->createOne();

        $response = $this->actingAs($user)->getJson(route('api.tasks.show', ['task' => $task->id + 1]));

        $response->assertStatus(404);
        $this->assertNotEmpty($response['message']);
    }
}
