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
            $this->request->filled('sortBy'),
            fn($query) => $query->orderBy(
                $this->request->input('sortBy')['key'] ?? '',
                $this->request->input('sortBy')['order'] ?? 'asc'
            )
        );
    }
}
