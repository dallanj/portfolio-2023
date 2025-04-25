<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\TagResource;

class ProjectResource extends JsonResource
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
            'hash' => $this->hash,
            'title' => $this->title,
            'description' => $this->description,
            'overview' => $this->overview,
            'created_at' => $this->created_at,
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'destroy_url' => route('projects.destroy', $this),
            'show_url' => route('projects.show', $this),
            'update_url' => route('projects.update', $this),
        ];
    }
}
