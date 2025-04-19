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

    private function cleanResume(array $delta): array
    {
        $cleanedOps = [];

        if (!isset($delta['ops']) || !is_array($delta['ops'])) {
            return ['ops' => [['insert' => "\n"]]];
        }

        foreach ($delta['ops'] as $op) {
            if (isset($op['insert']) && is_string($op['insert'])) {
                // Ensure block-level formats end with newline
                if (!str_ends_with($op['insert'], "\n") &&
                    isset($op['attributes']) &&
                    (isset($op['attributes']['header']) || isset($op['attributes']['align']) || isset($op['attributes']['list']))) {
                    $op['insert'] .= "\n";
                }
            }

            if (isset($op['insert']) && $op['insert'] !== null) {
                $cleanedOps[] = $op;
            }
        }

        // Ensure final op is a newline
        $last = end($cleanedOps);
        if (!isset($last['insert']) || (is_string($last['insert']) && !str_ends_with($last['insert'], "\n"))) {
            $cleanedOps[] = ['insert' => "\n"];
        }

        return ['ops' => $cleanedOps];
    }


    private function cleanDelta(array $delta)
    {
        $cleanedOps = [];

        foreach ($delta['ops'] as $op) {
            if (isset($op['insert']) && is_string($op['insert'])) {
                // If it's a header/list/align, enforce \n ending
                if (!str_ends_with($op['insert'], "\n") &&
                    isset($op['attributes']) &&
                    (isset($op['attributes']['header']) || isset($op['attributes']['align']) || isset($op['attributes']['list']))) {
                    $op['insert'] .= "\n";
                }
            }

            // Remove completely null inserts
            if ($op['insert'] !== null) {
                $cleanedOps[] = $op;
            }
        }

        $last = end($delta['ops']);
        if (!isset($last['insert']) || !str_ends_with($last['insert'], "\n")) {
            $delta['ops'][] = ['insert' => "\n"];
        }

        $delta['ops'] = $cleanedOps;
        // $cleaned = [];

        // foreach ($data['delta']['ops'] as $op) {
        //     if (!isset($op['insert'])) continue;
        
        //     $text = $op['insert'];
        //     $attributes = $op['attributes'] ?? [];
        
        //     // If the insert is a block of text with \n inside, split it and make each line end with a newline
        //     if (is_string($text)) {
        //         $lines = explode("\n", $text);
        //         foreach ($lines as $index => $line) {
        //             if ($line !== '') {
        //                 $cleaned[] = ['insert' => $line];
        //             }
        
        //             // Add newline after each line (except the very last if it's empty)
        //             if ($index < count($lines) - 1) {
        //                 $cleaned[] = ['insert' => "\n", 'attributes' => $attributes];
        //             }
        //         }
        //     } elseif ($text === '' || is_null($text)) {
        //         // Ensure empty blocks are represented as "\n"
        //         $cleaned[] = ['insert' => "\n", 'attributes' => $attributes];
        //     }
        // }
        

        // return ['ops' => $cleaned];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResumeRequest $request)
    {
        $data = $request->validated();

        // Clean delta if it has unnecessary newlines
        $data['delta'] = $this->cleanResume($data['delta']);

        dd('Incoming delta:', $data['delta']);

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
        $data['delta'] = $this->cleanResume($data['delta']);

        // if (empty($data['title'])) {
        //     // Count existing projects with "untitled" in the title (case-insensitive)
        //     $count = Project::where('title', 'LIKE', '%untitled%')->count();
    
        //     // Set title with the count
        //     $data['title'] = "Untitled-" . ($count + 1);
        // }

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
