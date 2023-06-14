<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\UserAssignedToTask;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;

class UpdateTaskAssigmentNotifiedAt
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
        if($event->notification instanceof UserAssignedToTask) {
            $task = $event->notification->task;
            $task->assignment_notified_at = now();
            $task->save();
        }
    }
}
