<?php

namespace App\Services;

// Models
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
        $mediaable = $attributes['mediaable'];  // Can be Resume or Project
        $file = $attributes['file'];  // File path
        $user = auth()->user();

        // Handle UploadedFile and String path cases
        if ($file instanceof UploadedFile) {
            $path = $file->storeAs("{$mediaable->getTable()}/{$mediaable->hash}", $file->hashName());
            $filename = $file->getClientOriginalName();
            $mimeType = dirname($file->getMimeType());
        } elseif (is_string($file) && Storage::exists($file)) {
            $filename = basename($file);
            $path = "{$mediaable->getTable()}/{$mediaable->hash}/{$filename}";
            // dd(Storage::get($path));
            // dd(File::get(Storage::get($path)));
            // Storage::put($path, File::get(Storage::path($file->path));
            // $file = Storage::get($path);
            // Storage::put($path, Storage::get($path));
            // dd('asda',$file);
            $mimeType = substr($path, strrpos($path, '.') + 1);
        } else {
            Log::error('Invalid file input in MediaService');
            return null;
        }

        if (!$path) {
            Log::error('Failed to save file in MediaService');
            return null;
        }

        try {
            $media = new Media();
            $media->filename = $filename;
            $media->path = $path;
            $media->type = $mimeType;
            $media->user_id = $user->id ?? null;

            $mediaable->media()->save($media);
            return $media;
        } catch (\Exception $e) {
            Log::error('MediaService create failed: ' . $e->getMessage());
            return null;
        }


        // $mediaable = $attributes['mediaable'];
        // $file = $attributes['file']; // Can be UploadedFile or string path
        // $user = auth()->user();

        // if ($file instanceof UploadedFile) {
        //     // Handle uploaded file
        //     $path = $file->storeAs("projects/{$mediaable->hash}", $file->hashName());
        //     $filename = $file->getClientOriginalName();
        //     $mimeType = $file->getMimeType();
        // } elseif (is_string($file) && Storage::exists($file->patj)) {
        //     // Handle generated file (like PDFs)s
        //     $filename = basename($file);
        //     $path = "resumes/{$mediaable->hash}/{$filename}";
        //     Storage::put($path, File::get($file)); // move into Laravel's storage
        //     $mimeType = File::mimeType($file);
        // } else {
        //     Log::error('Invalid file input in MediaService');
        //     return null;
        // }

        // if (!$path) {
        //     Log::error('Failed to save file in MediaService');
        //     return null;
        // }

        // try {
        //     $media = new Media();
        //     $media->filename = $filename;
        //     $media->path = $path;
        //     $media->type = dirname($mimeType);
        //     $media->user_id = $user->id ?? null;

        //     $mediaable->media()->save($media);
        //     return $media;
        // } catch (\Exception $e) {
        //     Log::error('MediaService create failed: ' . $e->getMessage());
        //     return null;
        // }



        // $mediaable = $attributes['mediaable'];
        // $file = $attributes['file'];
        
        // $user = auth()->user();

        // // Save the file locally
        // $path = $file->storeAs("projects/{$mediaable->hash}", $file->hashName());

        // if (!$path) {
        //     Log::error('Failed to save file');
        // }

        // try { 
        //     $media = new Media();
        //     $media->filename = $file->getClientOriginalName();
        //     $media->path = $path;
        //     $media->type = dirname($file->getMimeType());
        //     $media->user_id = $user->id;
        //     $mediaable->media()->save($media);
        // } catch (\Exception $e) {
        //     Log::error($e->getMessage());
        // }

        // return $media;
    }
}
