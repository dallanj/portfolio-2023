<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeRequest;
use App\Http\Requests\UpdateResumeRequest;
use Illuminate\Http\Request;
use App\PiniaStation\Facades\PiniaLoader;
use Illuminate\Http\JsonResponse;
use App\Models\Resume;
use Illuminate\Support\Str;
use App\Services\MediaService;

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

        return inertia('Projects/Index', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }
}
