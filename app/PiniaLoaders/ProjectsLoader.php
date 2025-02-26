<?php

namespace App\PiniaLoaders;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectsCollection;
use Illuminate\Support\Facades\Log;

class ProjectsLoader
{
    /**
     * Get side navigation based on user role
     * 
     * @return Collection
     */
    public function all()
    {
        // return Project::all();
        // dd(new ProjectsCollection(Project::get()));
        return (new ProjectsCollection(Project::paginate()))->resolve(request());
        // dd(new ProjectsCollection(Project::all()));
        // return ProjectResource::collection(Project::all());
    }
};
