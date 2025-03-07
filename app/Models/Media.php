<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ModelHashingTrait;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory,
        ModelHashingTrait,
        SoftDeletes;

    protected $fillable = [
        'name',
        'filename',
        'path',
        'type',
        'name',
        'order',
        'user_id',
        'mediaable_id',
        'mediaable_type',
    ];

    /**
     * Get all of the owning mediaable models.
     *
     * @return MorphTo
     */
    public function mediaable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who created the media.
     * 
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
