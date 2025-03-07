<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'title' => $this->filename,
            'description' => $this->path,
            'overview' => $this->name,
            'created_at' => $this->created_at,
            'order' => $this->order,
            'url' => "api/v1/media/{$this->hash}",//route('media.show', $this),
            'destroy_url' => route('media.destroy', $this),
            'update_url' => route('media.update', $this),
        ];
    }
}
