<?php

namespace App\Http\Resources\API\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'developer' => $this->developer,
            'cover_image_url' => $this->cover_image_url,
            'release_date' => $this->release_date,
            'genres' => GenreResource::collection($this->genres),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
