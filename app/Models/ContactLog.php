<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactLog extends Model
{
    use SoftDeletes, HasUlids;

    /** @var ULIDs are strings */
    protected $keyType = 'string';

    /** Disable auto-incrementing since ULIDs are not integers */
    public $incrementing = false;

    protected $fillable = [
        // 'id',
        'user_id',
        'logged_in',
        'ip',
        'user_agent',
        'status',
        'message',
        'channels',
        'country',
        'regionName',
        'city',
        'zip',
        'timezone',
        'lat',
        'lon',
        'isp',
        'as',
        'reverse',
        'mobile',
        'proxy',
        'hosting',
    ];

    /**
     * Get the parent contact model.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
