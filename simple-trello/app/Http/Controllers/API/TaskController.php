<?php

namespace App\Http\Controllers\API;

use App\Services\TaskService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\API\Task\TaskResource;

use App\Exceptions\API\UnauthorizedAPIException;
use App\Http\Requests\API\Task\TaskIndexRequest;
use App\Http\Requests\API\Task\TaskStoreRequest;
use App\Http\Requests\API\Task\TaskUpdateRequest;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(TaskIndexRequest $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'priority' => $request->get('priority'),
        ];
        $sortQueryString = $request->get('sort') ?? '';

        $tasks = $this->taskService->getAllFilteredAndSorted($filters, $sortQueryString);

        return TaskResource::collection($tasks);
    }

    public function store(TaskStoreRequest $request)
    {
        $task = $this->taskService->create($request->validated());

        return response(new TaskResource($task), 201);
    }

    public function show(int $id)
    {
        $task = $this->taskService->getById($id);

        return response(new TaskResource($task), 200);
    }

    public function update(TaskUpdateRequest $request, int $id)
    {
        $task = $this->taskService->getById($id);

        if (!Gate::allows('update', $task)) {
            throw new UnauthorizedAPIException();
        }

        $task = $this->taskService->update($task, $request->validated());

        return response(new TaskResource($task), 200);
    }

    public function destroy(int $id)
    {
        $task = $this->taskService->getById($id);

        if (!Gate::allows('delete', $task)) {
            throw new UnauthorizedAPIException();
        }

        $this->taskService->delete($task);

        return response()->noContent();
    }
}
