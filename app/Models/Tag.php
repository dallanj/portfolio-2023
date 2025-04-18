<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelHashingTrait;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory,
        ModelHashingTrait;

    /** @var array Searchable fields for model */
    public array $searchable = [];

    /**
     * Constructor.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Initialize searchable fields with dynamic role methods
        $this->searchable = [
            'name' => [],
        ];
    }

    protected $fillable = [
        'name',
        'slug'
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
