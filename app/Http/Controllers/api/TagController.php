<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use Illuminate\Http\Request;
use App\PiniaStation\Facades\PiniaLoader;
use Illuminate\Http\JsonResponse;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagController extends Controller
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
        PiniaLoader::load('tags', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request)
    {
        $data = $request->validated();

        if (empty($data['title'])) {
            // Count existing tags with "untitled" in the title (case-insensitive)
            $count = Tag::where('title', 'LIKE', '%untitled%')->count();
    
            // Set title with the count
            $data['title'] = "Untitled-" . ($count + 1);
        }

        $project = Tag::create([
            ...$data,
            'slug' => Str::slug($data['title'])
        ]);

        PiniaLoader::load('tags', 'active', $project);

        return PiniaLoader::toApiResponse();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $project)
    {
        $data = $request->validated();

        if (empty($data['title'])) {
            // Count existing tags with "untitled" in the title (case-insensitive)
            $count = Tag::where('title', 'LIKE', '%untitled%')->count();
    
            // Set title with the count
            $data['title'] = "Untitled-" . ($count + 1);
        }

        $project = Tag::updateOrCreate([
            'hash' => $project->hash,
        ], [
            ...$data,
            'slug' => Str::slug($data['title'])
        ]);

        PiniaLoader::load('tags', 'active', $project);

        return PiniaLoader::toApiResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $project)
    {
        $project->delete();

        PiniaLoader::load('tags', 'all');

        return inertia('Tags/Index', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }
}
