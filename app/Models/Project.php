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

    protected $fillable = [
        'title',
        'description',
        'overview',
        'slug',
        'order'
    ];

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($project) {
    //         $project->hash = Str::uuid();
    //     });
    // }

    public function media()
    {
        return $this->hasMany(Media::class)->orderBy('order');
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withPivot(['order'])->orderBy('pivot_order');
    }
}
