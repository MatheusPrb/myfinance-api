<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Code extends Model
{
    public $timestamps = false;
    protected $table = 'codes';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'id',
        'type',
        'email',
        'code',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'immutable_datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Code $code): void {
            if ($code->id === null || $code->id === '') {
                $code->id = (string) Str::uuid();
            }
        });
    }
}
