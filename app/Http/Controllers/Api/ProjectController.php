<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;
use App\PiniaStation\Facades\PiniaLoader;
use Illuminate\Http\JsonResponse;
use App\Models\Project;
use Illuminate\Support\Str;
use App\Services\MediaService;

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

        if (empty($data['title'])) {
            // Count existing projects with "untitled" in the title (case-insensitive)
            $count = Project::where('title', 'LIKE', '%untitled%')->count();
    
            // Set title with the count
            $data['title'] = "Untitled-" . ($count + 1);
        }

        $project = Project::create([
            ...$data,
            'slug' => Str::slug($data['title'])
        ]);

        if (!empty($data['tags'])) {
            $project->tags()->sync($data['tags']);
        }

        PiniaLoader::load('projects', 'active', $project);

        return PiniaLoader::toApiResponse();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();

        if (empty($data['title'])) {
            // Count existing projects with "untitled" in the title (case-insensitive)
            $count = Project::where('title', 'LIKE', '%untitled%')->count();
    
            // Set title with the count
            $data['title'] = "Untitled-" . ($count + 1);
        }

        $project = Project::updateOrCreate([
            'hash' => $project->hash,
        ], [
            ...$data,
            'slug' => Str::slug($data['title'])
        ]);

        if (!empty($data['tags'])) {
            $project->tags()->sync($data['tags']);
        }

        PiniaLoader::load('projects', 'active', $project);

        return PiniaLoader::toApiResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        PiniaLoader::load('projects', 'all');

        return inertia('Projects/Index', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function media(Project $project, StoreProjectRequest $request)
    {
        $data = $request->validated();

        if ($request->has('file')) {
            (new MediaService)->create([
                'mediaable' => $project,
                'file' => $data['file'],
            ]);
        }

        PiniaLoader::load('projects', 'active', $project);

        return PiniaLoader::toApiResponse();
    }
}
