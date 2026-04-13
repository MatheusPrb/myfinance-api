<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
