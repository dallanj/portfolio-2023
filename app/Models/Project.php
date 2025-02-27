<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ModelHashingTrait;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
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
            'title'         => [],
            'overview'      => [],
            'slug'          => [],
            'description'   => [],
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'overview',
        'slug',
        'order'
    ];

    /**
     * Get the media.
     */
    public function media()
    {
        return $this->hasMany(Media::class)->orderBy('order');
    }

    /**
     * Get the links.
     */
    public function links()
    {
        return $this->hasMany(Link::class);
    }

    /**
     * Get the tags.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withPivot(['order'])->orderBy('pivot_order');
    }
}
