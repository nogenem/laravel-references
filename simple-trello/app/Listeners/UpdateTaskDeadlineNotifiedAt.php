<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\TaskDeadlineIsNear;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;

class UpdateTaskDeadlineNotifiedAt
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Notifications\Events\NotificationSent  $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        if($event->notification instanceof TaskDeadlineIsNear) {
            $task = $event->notification->task;
            $task->deadline_notified_at = now();
            $task->save();
        }
    }
}
