<?php

namespace App\Services;

use App\Models\Task;
use App\Utils\QueryStringUtils;
use Illuminate\Support\Facades\DB;
use App\Notifications\UserAssignedToTask;
use App\Exceptions\API\CouldNotFindModelAPIException;
use App\Exceptions\API\CouldNotSaveModelAPIException;
use App\Exceptions\API\CouldNotDeleteModelAPIException;
use App\Exceptions\API\CouldNotUpdateModelAPIException;

class TaskService
{
    public function getAllFilteredAndSorted(array $filters, string $sortQueryString)
    {
        $query = Task::query()
            ->with(['createdBy', 'assignedTo']);

        foreach($filters as $key => $value) {
            if(!empty($value)) {
                $query->where($key, $value);
            }
        }

        $orderByObj = QueryStringUtils::sortQueryStringToOrderByObj($sortQueryString);
        if(!empty($orderByObj)) {
            $query->orderBy($orderByObj['field'], $orderByObj['dir']);
        } else {
            $query->orderBy('deadline', 'desc');
        }

        return $query->get();
    }

    public function create(array $attributes)
    {
        try {
            $created = Task::query()->create(
                $attributes + ['created_by' => auth()->id()]
            );

            if (!$created) {
                throw new CouldNotSaveModelAPIException(__('task'));
            }

            return $created;
        } catch (CouldNotSaveModelAPIException $e) {
            throw $e;
        } catch (\Exception $e) {
            logger()->error($e);

            throw new CouldNotSaveModelAPIException(__('task'));
        }
    }

    public function getById(int $id)
    {
        $task = Task::query()->find($id);

        if (!$task) {
            throw new CouldNotFindModelAPIException(_('task'));
        }

        return $task;
    }

    public function update(Task $task, array $attributes)
    {
        DB::beginTransaction();
        try {
            $oldAssignedTo = $task->assigned_to;

            if(isset($attributes['assigned_to']) && $oldAssignedTo != $attributes['assigned_to']) {
                $attributes['deadline_notified_at'] = null;
                $attributes['assignment_notified_at'] = null;
            }

            $updated = $task->update($attributes);

            if (!$updated) {
                throw new CouldNotUpdateModelAPIException(__('task'));
            }

            $newAssignedTo = $task->assigned_to;

            if($oldAssignedTo != $newAssignedTo) {
                $task->assignedTo->notify((new UserAssignedToTask($task))->afterCommit());
            }

            DB::commit();

            return $task;
        } catch (CouldNotUpdateModelAPIException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            logger()->error($e);

            throw new CouldNotUpdateModelAPIException(__('task'));
        }
    }

    public function delete(Task $task)
    {
        try {
            $deleted = $task->delete();

            if (!$deleted) {
                throw new CouldNotDeleteModelAPIException(__('task'));
            }
        } catch (CouldNotDeleteModelAPIException $e) {
            throw $e;
        } catch (\Exception $e) {
            logger()->error($e);

            throw new CouldNotDeleteModelAPIException(__('task'));
        }
    }
}
