<?php

namespace Tests\Feature\API\Task;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_delete_tasks_by_id(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $task = Task::factory()->createOne();

        $response = $this->actingAs($user)->deleteJson(route('api.tasks.destroy', ['task' => $task->id]));

        $response->assertStatus(204);

        $deletedTask = Task::where('id', $task->id)->first();
        $this->assertNull($deletedTask);
    }

    public function test_unauthenticated_users_cant_delete_tasks_by_id(): void
    {
        $task = Task::factory()->createOne();

        $response = $this->deleteJson(route('api.tasks.destroy', ['task' => $task->id]));

        $response->assertStatus(401);
        $this->assertNotEmpty($response['message']);

        $deletedTask = Task::where('id', $task->id)->first();
        $this->assertNotNull($deletedTask);
    }
}
