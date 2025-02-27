<?php

namespace App\PiniaLoaders;

use App\Models\Project;
use Illuminate\Support\Collection;
use App\Http\Resources\ProjectsCollection;
use Illuminate\Support\Facades\Pipeline;
use App\Filters\Searchable;
use App\Filters\Sortable;

class ProjectsLoader
{
    /**
     * Get side navigation based on user role
     * 
     * @return Collection
     */
    public function all()
    {
        $projects = Pipeline::send(Project::query())
            ->through([
                Searchable::class,
                Sortable::class,
            ])
            ->thenReturn()
            ->paginate(request()->per_page ?? 4);

        return (new ProjectsCollection($projects))->resolve(request());    
    }
};
