<?php

namespace App\Services;

// Models
use App\Models\Media;
use Illuminate\Http\UploadedFile;
// Support Facades
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;

class MediaService
{    
    /**
     * Create media
     *
     * @param  mixed $attributes
     * @return void
     */
    public function create(array $attributes)
    {
        $mediaable = $attributes['mediaable'];
        $file = $attributes['file'];
        
        $user = auth()->user();
        $uuid = Str::uuid();

        // Save the file locally
        $path = $file->storeAs("projects/$uuid", $file->hashName());

        if (!$path) {
            Log::error('Failed to save file');
        }

        try { 
            $media = new Media();
            $media->filename = $file->getClientOriginalName();
            $media->path = $path;
            $media->type = dirname($file->getMimeType());
            $media->user_id = $user->id;
            $mediaable->media()->save($media);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $media;
    }
}
