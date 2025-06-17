<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeRequest;
use App\Http\Requests\UpdateResumeRequest;
use App\Http\Requests\BulkDeleteResumesRequest;
use Illuminate\Http\Request;
use App\PiniaStation\Facades\PiniaLoader;
use Illuminate\Http\JsonResponse;
use App\Models\Resume;
use Illuminate\Support\Str;
use App\Services\MediaService;
use App\Helpers\PdfGenerator;
use App\Http\Requests\DraftResumeRequest;
use App\Http\Requests\PublishResumeRequest;
use Illuminate\Support\Facades\Storage;

class ResumeController extends Controller
{
    /**
     * Search media on the library page
     *
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        PiniaLoader::load('resumes', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Format Delta Ops beforing storing or updating
     * (TODO: Move to a service class)
     *
     * @param array $delta
     * 
     * @return array
     */
    private function formatDelta(array $delta): array
    {
        $ops = $delta['ops'];
        $formatted = [];
    
        $total = count($ops);
    
        foreach ($ops as $i => $op) {
            $insert = $op['insert'] ?? null;
            $attributes = $op['attributes'] ?? null;
            $isLast = $i === $total - 1;
    
            // If insert is null and has attributes (like header), treat it as a formatted newline
            if ($insert === null && $attributes) {
                $formatted[] = [
                    'insert' => "\n",
                    'attributes' => $attributes,
                ];
                continue;
            }
    
            // If it's a plain text insert
            if (is_string($insert)) {
                // If it ends with \n and it's not the last item and has no attributes,
                // strip it (we'll add it to the last one only)
                if (!$isLast && !$attributes && str_ends_with($insert, "\n")) {
                    $insert = rtrim($insert, "\n");
                }
    
                // If it's the last item and it doesn't end with \n, append it
                if ($isLast && !str_ends_with($insert, "\n")) {
                    $insert .= "\n";
                }
    
                $formattedOp = ['insert' => $insert];
                if ($attributes) {
                    $formattedOp['attributes'] = $attributes;
                }
    
                $formatted[] = $formattedOp;
            }
        }
    
        return ['ops' => $formatted];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResumeRequest $request)
    {
        $data = $request->validated();

        // Clean delta if it has unnecessary newlines
        $data['delta'] = $this->formatDelta($data['delta']);

        if (empty($data['title'])) {
            // Count existing resumes with "untitled" in the title (case-insensitive)
            $count = Resume::where('title', 'LIKE', '%untitled%')->count();
    
            // Set title with the count
            $data['title'] = "Untitled-" . ($count + 1);
        }

        $resume = Resume::create([
            ...$data,
            'version' => Resume::where('title', $data['title'])->max('version') + 1 ?? 1,
        ]);

        if (!$data['is_draft']) {
            // Set all other resumes as draft
            Resume::where('id', '!=', $resume->id ?? null) // in case you're updating
                ->update(['is_draft' => true]);
        }

        // Path to save the PDF (e.g., resumes/{resume_id}.pdf)
        $path = "resumes/{$resume->hash}.pdf";
        $absolutePath = storage_path("app/{$path}");

        // Save PDF file
        PdfGenerator::fromHtml($data['html'], $absolutePath);

        // Save to disk
        if (!$resume->media) {
            (new MediaService)->create([
                'mediaable' => $resume,
                'file' => $path, // Store just the relative path
            ]);
        } else {
            // Update existing media record (e.g., if you store the path)
            $resume->media->update([
                'file' => $path, // Overwrite if needed or just leave if path stays same
            ]);
            
            // Optional: If you want to re-save the file every time
            Storage::put($path, file_get_contents($absolutePath));
        }

        PiniaLoader::load('resumes', 'active', $resume);

        return PiniaLoader::toApiResponse();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResumeRequest $request, Resume $resume)
    {
        $data = $request->validated();

        // Clean delta if it has unnecessary newlines
        $data['delta'] = $this->formatDelta($data['delta']);

        if (empty($data['title'])) {
            // Count existing projects with "untitled" in the title (case-insensitive)
            $count = Resume::where('title', 'LIKE', '%untitled%')->count();
    
            // Set title with the count
            $data['title'] = "Untitled-" . ($count + 1);
        }

        $resume = Resume::updateOrCreate([
            'hash' => $resume->hash,
        ], [
            ...$data,
            'version' => Resume::where('title', $data['title'])->max('version') + 1 ?? 1,
        ]);

        if (!$data['is_draft']) {
            // Set all other resumes as draft
            Resume::where('id', '!=', $resume->id ?? null) // in case you're updating
                ->update(['is_draft' => true]);
        }

        // Path to save the PDF for Resume
        $path = "resumes/{$resume->hash}.pdf";
        $absolutePath = storage_path("app/{$path}");

        // Save PDF file
        PdfGenerator::fromHtml($data['html'], $absolutePath);

        // Store PDF for Resume media
        if (!$resume->media) {
            (new MediaService)->create([
                'mediaable' => $resume,  // Associating the media with the Resume model
                'file' => $path,
            ]);
        } else {
            $resume->media->update([
                'file' => $path, 
            ]);
            
            Storage::put($path, file_get_contents($absolutePath));
        }
        

        PiniaLoader::load('resumes', 'active', $resume);

        return PiniaLoader::toApiResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resume $resume)
    {
        $resume->delete();

        PiniaLoader::load('resumes', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $resume = Resume::whereIsDraft(false)->first();
        
        return response()->file(Storage::path($resume->media->path), [
            'Content-Disposition' => "attachment; filename=\"{$resume->media->filename}\"",
            'Content-Type' => Storage::mimeType($resume->media->path),
        ]);
    }

    /**
     * Publish resume and set any other published resume to draft for the specified resources from storage.
     */
    public function publish(PublishResumeRequest $request)
    {
        $ids = $request->input('ids', []);

        // Set all currently published resumes to draft
        Resume::where('is_draft', false)->update(['is_draft' => true]);

        // Publish the selected resume
        Resume::whereIn('id', $ids)->update(['is_draft' => false]);

        PiniaLoader::load('resumes', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Set resume as a draft for the specified resources from storage.
     */
    public function draft(DraftResumeRequest $request)
    {
        $ids = $request->input('ids', []);
        Resume::whereIn('id', $ids)->update(['is_draft' => true]);

        PiniaLoader::load('resumes', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Remove the mass resources from storage.
     */
    public function bulkDelete(BulkDeleteResumesRequest $request)
    {
        $ids = $request->input('ids', []);
        Resume::whereIn('id', $ids)->delete();

        PiniaLoader::load('resumes', 'all');

        return PiniaLoader::toApiResponse();
    }
}
