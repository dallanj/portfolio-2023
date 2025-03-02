<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;
use App\PiniaStation\Facades\PiniaLoader;
use Illuminate\Http\JsonResponse;
use App\Models\Project;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Search media on the library page
     *
     * @param UploadMediaRequest $request
     * 
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        PiniaLoader::load('projects', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();

        $project = Project::create([
            ...$data,
            'slug' => Str::slug($data['title'])
        ]);

        PiniaLoader::load('projects', 'active', $project);

        return inertia('Projects/Create', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();

        $project = Project::updateOrCreate(
            [
                'hash' => $project->hash,  // Match on language code
            ],
            [
                ...$data,
                'slug' => Str::slug($data['title'])
            ]   
        );

        PiniaLoader::load('projects', 'active', $project);

        return inertia('Projects/Create', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
