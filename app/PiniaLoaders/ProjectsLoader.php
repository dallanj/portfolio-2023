<?php

namespace App\PiniaLoaders;

use App\Models\Project;
use Illuminate\Support\Collection;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectsCollection;
use Illuminate\Support\Facades\Pipeline;
use App\Filters\Searchable;
use App\Filters\Sortable;

class ProjectsLoader
{
    /**
     * Get all projects
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
            ->thenReturn();

        if (request()->boolean('paginate', true)) {
            $projects = $projects->paginate(request()->per_page ?? 8);
        } else {
            $projects = $projects->get();
        }

        $projects->loadMissing(['media', 'tags']);
        
        if ($projects instanceof \Illuminate\Pagination\Paginator || $projects instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return (new ProjectsCollection($projects))->resolve(request());
        }
    
        // If not paginated, transform manually
        return ProjectResource::collection($projects)->resolve(request()); 
    }

    /**
     * Get the selected project
     * 
     * @param Project $project
     * 
     * @return Collection
     */
    public function active(Project $project)
    {
        $project->loadMissing(['media', 'tags']);
        
        return (new ProjectResource($project))->resolve(request());
    }
};
