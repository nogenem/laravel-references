<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use DavidBadura\FakerMarkdownGenerator\FakerProvider;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $this->faker->addProvider(new FakerProvider($this->faker));

        $users = User::select('id')->get()->pluck('id')->toArray();
        $statuses = array_values(Task::STATUSES);
        $priorities = array_values(Task::PRIORITIES);
        $now = new \DateTime();

        $status = $this->faker->randomElement($statuses);
        $deadline = $this->faker->dateTimeBetween('-5 days', '+15 days');

        $assignedTo = null;
        if($status == Task::STATUSES['CONCLUDED'] || $this->faker->randomDigit() >= 5) {
            $assignedTo = $this->faker->randomElement($users);
        }

        $deadlineNotifiedAt = null;
        if($deadline <= $now || $status == Task::STATUSES['CONCLUDED'] || $now->diff($deadline)->days <= 2) {
            $deadlineNotifiedAt = $now;
        }

        return [
            'title' => trim(substr($this->faker->sentence(15), 0, 100)),
            'description' => trim($this->faker->markdown()),
            'status' => $status,
            'priority' => $this->faker->randomElement($priorities),
            'deadline' => $deadline,
            'created_by' => $this->faker->randomElement($users),
            'assigned_to' => $assignedTo,
            'deadline_notified_at' => $deadlineNotifiedAt,
            'assignment_notified_at' => $now,
        ];
    }
}
