<?php

namespace App\Http\Resources\API\Task;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\User\SimpleUserResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'deadline' => $this->deadline,
            'createdBy' => new SimpleUserResource($this->createdBy),
            'assignedTo' => new SimpleUserResource($this->assignedTo),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
