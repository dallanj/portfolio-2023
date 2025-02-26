<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'current_page' => $this->resource->currentPage(),
            'data' => ProjectResource::collection($this->collection),
            'first_page_url' => $this->resource->url(1),
            'from' => 1,
            'last_page' => $this->resource->lastPage(),
            'last_page_url' => $this->resource->url($this->resource->lastPage()),
            'links' => [],
            'next_page_url' => $this->resource->nextPageUrl(),
            'path' => $request->url(),
            'per_page' => $this->resource->perPage(),
            'prev_page_url' => $this->resource->previousPageUrl() ?? null,
            'to' => $this->resource->count(),
            'total' => $this->resource->total(),
        ];
    }
}
