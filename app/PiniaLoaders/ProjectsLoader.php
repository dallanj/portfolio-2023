<?php

namespace App\PiniaLoaders;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Support\Collection;
use App\Http\Resources\ProjectsCollection;
use Illuminate\Support\Facades\Pipeline;
use App\Filters\BySearchable;

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
            ->through(BySearchable::class)
            ->thenReturn()
            ->paginate(request()->per_page ?? 4);

        return (new ProjectsCollection($projects))->resolve(request());    
    }
};
