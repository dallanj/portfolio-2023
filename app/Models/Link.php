<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelHashingTrait;

class Link extends Model
{
    /** @use HasFactory<\Database\Factories\LinkFactory> */
    use HasFactory,
        ModelHashingTrait;

    protected $fillable = [
        'project_id',
        'type',
        'url'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
