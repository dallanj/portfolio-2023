<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Sortable
{
    public function __construct(public Request $request){}

    /**
     * Query by sorted columns
     * 
     * @return Builder
     */
    public function handle(Builder $query, Closure $next)
    {
        return $next($query)->when(
            $this->request->has('sortBy'),
            function ($query) {
                foreach ($this->request->input('sortBy', []) as $sort) {
                    if (isset($sort['key']) && isset($sort['order'])) {
                        $query->orderBy($sort['key'], $sort['order']);
                    }
                }
            }
        );
    }
}
