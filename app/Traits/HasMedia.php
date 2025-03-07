<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMedia
{
    /**
     * Get the media
     *
     * @return MorphMany
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'mediaable');
    }
}
