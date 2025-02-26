<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;
use App\PiniaStation\Facades\PiniaLoader;
use Illuminate\Http\JsonResponse;
use App\Models\Project;

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
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'overview' => 'nullable|string',
            'slug' => 'required|string|unique:projects,slug',
        ]);

        $project = Project::create($data);
        return redirect()->route('projects.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
