<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskDeadlineIsNear extends Notification
{
    use Queueable;

    public $task;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/tasks?selectedTaskId=' . $this->task->id);
        $diffInDays = config('tasks.difference_in_days_to_notify_task_deadline');

        $timezone = ($notifiable->timezone) ?? config('app.timezone');
        $deadline = $this->task->deadline->setTimezone($timezone);

        return (new MailMessage())
            ->subject(__("notification.task_deadline_is_in_x_days", ['days' => $diffInDays]))
            ->greeting(__('Hello') . ' ' . $notifiable->name . '.')
            ->line(__("notification.task_deadline_is_in_x_days", ['days' => $diffInDays]))
            ->line(__('Title') . ': ' . $this->task->title)
            ->line(__('Deadline') . ': ' . $deadline)
            ->line(__('Priority') . ': ' . $this->task->priority)
            ->action(__('notification.view_task'), $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->task;
    }
}
