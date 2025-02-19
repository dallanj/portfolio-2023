<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ModelHashingTrait;

class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory,
        ModelHashingTrait,
        SoftDeletes;

    protected $fillable = [
        'project_id',
        'path',
        'type',
        'filename',
        'order'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
