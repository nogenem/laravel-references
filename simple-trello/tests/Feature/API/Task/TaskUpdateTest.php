<?php

namespace Tests\Feature\API\Task;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Notifications\UserAssignedToTask;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_update_tasks(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $savedTask = Task::factory()->createOne();

        $requestBody = $savedTask->toArray();
        $requestBody['title'] = 'Edited Title';

        $response = $this->actingAs($user)->putJson(
            route('api.tasks.update', ['task' => $savedTask->id]),
            $requestBody
        );

        $response->assertStatus(200);
        $response->assertJsonPath('title', $requestBody['title']);

        $updatedTask = $savedTask->fresh();
        $this->assertEquals($updatedTask->title, $requestBody['title']);
    }

    public function test_users_are_notified_when_a_task_is_assigned_to_them(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $savedTask = Task::factory()->createOne(['assigned_to' => $user->id]);
        $otherUser = User::factory()->createOne();

        $requestBody = $savedTask->toArray();
        $requestBody['title'] = 'Edited Title';
        $requestBody['assigned_to'] = $otherUser->id;

        Notification::fake();

        $response = $this->actingAs($user)->putJson(
            route('api.tasks.update', ['task' => $savedTask->id]),
            $requestBody
        );

        Notification::assertSentTo(
            [$otherUser],
            UserAssignedToTask::class
        );

        $response->assertStatus(200);
        $response->assertJsonPath('title', $requestBody['title']);

        $updatedTask = $savedTask->fresh();
        $this->assertEquals($updatedTask->title, $requestBody['title']);
        $this->assertEquals($updatedTask->assigned_to, $requestBody['assigned_to']);
    }

    public function test_tasks__notified_at__fields_are_updated_after_new_assigment(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $savedTask = Task::factory()->createOne([
            'assigned_to' => $user->id,
            'deadline_notified_at' => new \DateTime(),
        ]);
        $otherUser = User::factory()->createOne();

        $requestBody = $savedTask->toArray();
        $requestBody['title'] = 'Edited Title';
        $requestBody['assigned_to'] = $otherUser->id;

        $response = $this->actingAs($user)->putJson(
            route('api.tasks.update', ['task' => $savedTask->id]),
            $requestBody
        );

        $response->assertStatus(200);
        $response->assertJsonPath('title', $requestBody['title']);

        $updatedTask = $savedTask->fresh();
        $this->assertEquals($updatedTask->title, $requestBody['title']);
        $this->assertEquals($updatedTask->assigned_to, $requestBody['assigned_to']);
        $this->assertNotNull($updatedTask->assignment_notified_at);
        $this->assertNull($updatedTask->deadline_notified_at);
    }

    public function test_unauthenticated_users_cant_update_tasks(): void
    {
        $savedTask = Task::factory()->createOne();

        $requestBody = $savedTask->toArray();
        $requestBody['title'] = 'Edited Title';

        $response = $this->putJson(
            route('api.tasks.update', ['task' => $savedTask->id]),
            $requestBody
        );

        $response->assertStatus(401);
        $this->assertNotEmpty($response['message']);

        $updatedTask = $savedTask->fresh();
        $this->assertNotEquals($updatedTask->title, $requestBody['title']);
    }

    public function test_users_can_patch_tasks_without_required_fields(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $savedTask = Task::factory()->createOne();

        $requestBody = ['description' => 'Updated description', 'status' => null];

        $response = $this->actingAs($user)->patchJson(
            route('api.tasks.update', ['task' => $savedTask->id]),
            $requestBody
        );

        $response->assertStatus(200);
        $response->assertJsonPath('description', $requestBody['description']);
        $response->assertJsonPath('status', null);

        $updatedTask = $savedTask->fresh();
        $this->assertEquals($updatedTask->description, $requestBody['description']);
        $this->assertEquals($updatedTask->status, $requestBody['status']);
    }

    public function test_users_cant_put_tasks_without_required_fields(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $savedTask = Task::factory()->createOne();

        $requestBody = ['description' => 'Updated description'];

        $response = $this->actingAs($user)->putJson(
            route('api.tasks.update', ['task' => $savedTask->id]),
            $requestBody
        );

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $updatedTask = $savedTask->fresh();
        $this->assertNotEquals($updatedTask->description, $requestBody['description']);
    }

    public function test_users_cant_update_tasks_with_invalid_status(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $savedTask = Task::factory()->createOne();

        $requestBody = $savedTask->toArray();
        $requestBody['title'] = 'Edited Title';
        $requestBody['status'] = 'SOME_STATUS';

        $response = $this->actingAs($user)->putJson(
            route('api.tasks.update', ['task' => $savedTask->id]),
            $requestBody
        );

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $updatedTask = $savedTask->fresh();
        $this->assertNotEquals($updatedTask->title, $requestBody['title']);
        $this->assertNotEquals($updatedTask->status, $requestBody['status']);
    }

    public function test_users_cant_update_tasks_with_invalid_priority(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $savedTask = Task::factory()->createOne();

        $requestBody = $savedTask->toArray();
        $requestBody['title'] = 'Edited Title';
        $requestBody['priority'] = 'SOME_PRIORITY';

        $response = $this->actingAs($user)->putJson(
            route('api.tasks.update', ['task' => $savedTask->id]),
            $requestBody
        );

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $updatedTask = $savedTask->fresh();
        $this->assertNotEquals($updatedTask->title, $requestBody['title']);
        $this->assertNotEquals($updatedTask->priority, $requestBody['priority']);
    }

    public function test_users_cant_update_tasks_with_invalid_assigned_to(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->createOne();
        $savedTask = Task::factory()->createOne();

        $requestBody = $savedTask->toArray();
        $requestBody['assigned_to'] = $user->id + 1;

        $response = $this->actingAs($user)->putJson(
            route('api.tasks.update', ['task' => $savedTask->id]),
            $requestBody
        );

        $response->assertStatus(422);
        $this->assertNotEmpty($response['message']);

        $updatedTask = $savedTask->fresh();
        $this->assertNotEquals($updatedTask->assigned_to, $requestBody['assigned_to']);
    }
}
