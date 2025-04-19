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
        $resumes = Pipeline::send(Resume::query())
            ->through([
                Searchable::class,
                Sortable::class,
            ])
            ->thenReturn()
            ->paginate(request()->per_page ?? 8);

        return $resumes;  
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
