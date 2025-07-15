<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Sortable
{
    public function __construct(public Request $request){}

    /**
     * Query by multiple sorted columns
     * 
     * @return Builder
     */
    public function handle(Builder $query, Closure $next)
    {
        $sortBy = $this->request->input('sortBy');

        if (is_array($sortBy)) {
            foreach ($sortBy as $sort) {
                if (!isset($sort['key']) || !isset($sort['order'])) continue;
                $query->orderBy($sort['key'], $sort['order']);
            }
        }

        // must happen AFTER applying orderBy
        return $next($query);
    }
}
