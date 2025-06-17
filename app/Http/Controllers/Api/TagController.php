<?php

namespace App\Http\Controllers\Api;

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

        $tag = Tag::create([
            ...$data,
            'slug' => Str::slug($data['name'])
        ]);

        PiniaLoader::load('tags', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $data = $request->validated();

        $tag = Tag::updateOrCreate([
            'hash' => $tag->hash,
        ], [
            ...$data,
            'slug' => Str::slug($data['name'])
        ]);

        PiniaLoader::load('tags', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        PiniaLoader::load('tags', 'all');

        return inertia('Tags/Index', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }
}
