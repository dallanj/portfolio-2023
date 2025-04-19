<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelHashingTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory,
        ModelHashingTrait,
        SoftDeletes;

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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active'  => 'boolean'
    ];

    /**
     * Get the projects assigned to.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
