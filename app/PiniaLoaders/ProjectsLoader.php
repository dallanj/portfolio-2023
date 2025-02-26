<?php

namespace App\PiniaLoaders;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Support\Collection;
use App\Http\Resources\ProjectsCollection;

class ProjectsLoader
{
    /**
     * Get side navigation based on user role
     * 
     * @return Collection
     */
    public function all()
    {
        return (new ProjectsCollection(Project::paginate(2)))->resolve(request());    
    }
};
