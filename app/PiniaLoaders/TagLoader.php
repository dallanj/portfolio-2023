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
        $tags = Pipeline::send(Tag::query())
            ->through([
                Searchable::class,
                Sortable::class,
            ])
            ->thenReturn()
            ->paginate(request()->per_page ?? 8);

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
