<?php

namespace Tests\Feature\API\Task;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_store_tasks(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $task = Task::factory()->makeOne()->toArray();

        $response = $this->actingAs($user)->postJson(route('api.tasks.store'), $task);

        $response->assertStatus(201);
        $response->assertJsonPath('title', $task['title']);
        $response->assertJsonPath('status', $task['status']);
        $response->assertJsonPath('priority', $task['priority']);

        $savedTask = Task::where('title', $task['title'])->first();
        $this->assertNotNull($savedTask);
        $response->assertJsonPath('title', $savedTask['title']);
        $response->assertJsonPath('status', $savedTask['status']);
        $response->assertJsonPath('priority', $savedTask['priority']);
    }

    public function test_unauthenticated_users_cant_store_tasks(): void
    {
        $task = Task::factory()->makeOne()->toArray();

        $response = $this->postJson(route('api.tasks.store'), $task);

        $response->assertStatus(401);
        $this->assertNotEmpty($response['message']);

        $taskCount = Task::where('title', $task['title'])->count();
        $this->assertEquals(0, $taskCount);
    }

    public function test_users_can_store_tasks_passing_only_the_title(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $task = ['title' => 'Some title'];

        $response = $this->actingAs($user)->postJson(route('api.tasks.store'), $task);

        $response->assertStatus(201);
        $response->assertJsonPath('title', $task['title']);

        $savedTask = Task::where('title', $task['title'])->first();
        $this->assertNotNull($savedTask);
    }

    public function test_users_cant_store_tasks_with_invalid_status(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $task = Task::factory()->makeOne(['status' => 'SOME_STATUS'])->toArray();

        $response = $this->actingAs($user)->postJson(route('api.tasks.store'), $task);

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $taskCount = Task::where('title', $task['title'])->count();
        $this->assertEquals(0, $taskCount);
    }

    public function test_users_cant_store_tasks_with_invalid_priority(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $task = Task::factory()->makeOne(['priority' => 'SOME_PRIORITY'])->toArray();

        $response = $this->actingAs($user)->postJson(route('api.tasks.store'), $task);

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $taskCount = Task::where('title', $task['title'])->count();
        $this->assertEquals(0, $taskCount);
    }
}
