<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Notifications\TaskDeadlineIsNear;
use App\Notifications\UserAssignedToTask;

class SendTaskDeadlineEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-deadline-emails
        {--D|debug : Print info about the code execution}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification when task `deadline` is near.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $debug = $this->option('debug');
        $startTime = microtime(true);

        $tasksToBeNotified = Task::query()
            ->with(['assignedTo'])
            ->whereNotNull('assigned_to')
            ->whereNull('deadline_notified_at')
            ->where(DB::raw('DATEDIFF(deadline, NOW())'), '<=', config('tasks.difference_in_days_to_notify_task_deadline'))
            ->where('status', '<>', Task::STATUSES['CONCLUDED'])
            ->get();

        if($debug) {
            $this->info(sprintf("Found %d tasks to be notified.", count($tasksToBeNotified)));
        }

        foreach($tasksToBeNotified as $task) {
            $task->assignedTo->notify(new TaskDeadlineIsNear($task));

            if($debug) {
                $this->info(
                    sprintf("\tNotifying user %d about task %d that has deadline for %s.", $task->assignedTo->id, $task->id, $task->deadline)
                );
            }
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        if($debug) {
            $this->info(sprintf("Command took %f seconds to run.", $executionTime));
        }

        return 0;
    }
}
