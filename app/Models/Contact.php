<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\ModelHashingTrait;

class Contact extends Model
{
    use ModelHashingTrait,
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
            'name'  => [],
            'email' => [],  
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'message',
        'pgp_key',
        'is_read',
        'is_important',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active'     => 'boolean',
        'is_important'  => 'boolean',
    ];

    /**
     * Get the contact message.
     */
    public function contactLogs(): HasOne
    {
        return $this->hasOne(ContactLog::class);
    }
}
