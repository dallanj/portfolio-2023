<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Schema;
use App\Models\User as ModelsUser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Searchable
{
    protected ?ModelsUser $user;

    public function __construct(protected Request $request)
    {
        $this->user = auth()->user();
    }

    /**
     * Apply filter based on a single search term across multiple fields
     * depending on the user's role.
     *
     * @param Builder $query
     * @param Closure $next
     * @return Builder
     */
    public function handle(Builder $query, Closure $next)
    {
        if ($this->request->has('term') && !empty($this->request->term)) {
            // Get the model being queried
            $model = $query->getModel(); 
            // Get the table of the model
            $table = $model->getTable();

            // 1. Seperate columns and raw queries and remove any column not in the database
            $searchables = Collection::make($model?->searchable)
                ->filter(fn ($values, $column) => Schema::hasColumn($table, $column) || isset($values['query']))
                ->groupBy(fn ($values, $column) => Schema::hasColumn($table, $column) ? 'columns' : 'raw', true);

            // 2. Begin search query
            $query->where(function ($q) use ($searchables, $table) {
                $columns = $searchables->get('columns');
                
                // Search dynamic columns from the model
                $columns->each(function ($fields, $key) use ($q, $table) {
                    if (isset($fields['roles']) && !$this->hasAccess($fields['roles'])) {
                        return;
                    }
                    $q->orWhere("$table.$key", 'regexp', $this->request->term);
                });

                // Raw SQL
                $raw = $searchables->get('raw');

                if ($raw !== null) {
                    // Apply SQL to columns from the model
                    $raw->each(function ($fields, $key) use ($q) {
                        if (isset($fields['roles']) && !$this->hasAccess($fields['roles'])) {
                            return;
                        }
                        $sql = is_array($fields['query']) ? collect($fields['query'])->flatten()->toArray() : $fields['query'];
                        
                        if ($sql instanceof string) {
                            $q->orWhere(DB::raw($sql), 'like', '%'.$this->request->term.'%');
                        } else {
                            foreach ($sql as $statement) {
                                $q->orWhere(DB::raw($statement), 'like', '%'.$this->request->term.'%');
                            } 
                        }
                    });
                }
            });
        }

        return $next($query);
    }

    /**
     * Checks if user is unauthenticated and a guest
     *
     * @param array $roles
     * @return bool
     */
    protected function hasAccess(array $roles): bool
    {
        return $this->user && $this->user->hasAnyRole($roles);
    }
}
