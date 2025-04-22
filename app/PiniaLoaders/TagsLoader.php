<?php

namespace App\PiniaLoaders;

use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Pipeline;
use App\Filters\Searchable;
use App\Filters\Sortable;

class TagsLoader
{
    /**
     * Get all tags
     * 
     * @return Collection
     */
    public function all()
    {
        $tags = Pipeline::send(Tag::query()->whereIsActive(true))
            ->through([
                Searchable::class,
                Sortable::class,
            ])
            ->thenReturn();

        if (request()->boolean('paginate', true)) {
            $tags = $tags->paginate(request()->per_page ?? 8);
        } else {
            $tags = $tags->get();
        }
        
        if ($tags instanceof \Illuminate\Pagination\Paginator || $tags instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            // return (new ProjectsCollection($projects))->resolve(request());
            return $tags;
        }
    
        // If not paginated, transform manually
        // return ProjectResource::collection($projects)->resolve(request());  
        return $tags;
    }

    /**
     * Get the selected tag
     * 
     * @param Tag $tag
     * 
     * @return Collection
     */
    public function active(Tag $tag)
    {
        return $tag;
    }
};
