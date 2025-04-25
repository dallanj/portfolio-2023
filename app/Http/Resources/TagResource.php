<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'order' => $this->order,
            'is_active' => $this->is_active
        ];
    }
}
