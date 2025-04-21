<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ModelHashingTrait;

class Resume extends Model
{
    use SoftDeletes,
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
            'title'     => [],
            'version'   => [],
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'version',
        'delta',
        'html',
        'is_draft',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'delta'     => 'array',
        'is_draft'  => 'boolean'
    ];

    /**
     * Get the resume PDF.
     */
    public function media()
    {
        return $this->morphOne(Media::class, 'mediaable');
    }
}
