<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property CarbonImmutable|null $created_at
 */
class ApplicationLog extends Model
{
    public $timestamps = false;

    protected $table = 'logs';

    protected $fillable = [
        'channel',
        'level',
        'message',
        'context',
        'extra',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'extra' => 'array',
            'created_at' => 'immutable_datetime',
        ];
    }
}
