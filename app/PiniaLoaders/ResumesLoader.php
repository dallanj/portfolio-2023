<?php

namespace App\PiniaLoaders;

use App\Models\Resume;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Pipeline;
use App\Filters\Searchable;
use App\Filters\Sortable;

class ResumesLoader
{
    /**
     * Get all resumes
     * 
     * @return Collection
     */
    public function all()
    {
        $query = Resume::query();

        $resumes = Pipeline::send($query)
            ->through([
                Searchable::class,
                Sortable::class,
            ])
            ->thenReturn();

        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 8);

        // Use Paginator with fallback to previous page if current page is empty
        $paginated = $resumes->paginate($perPage, ['*'], 'page', $page);

        if ($paginated->isEmpty() && $page > 1) {
            // Go to previous page if current is now empty due to deletion
            $paginated = $resumes->paginate($perPage, ['*'], 'page', $page - 1);
        }

        return $paginated;
    }

    /**
     * Get the selected resume
     * 
     * @param Resume $resume
     * 
     * @return Collection
     */
    public function active(Resume $resume)
    {
        return $resume;
    }
};
