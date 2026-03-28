<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    use HasUuids;

    protected $table = 'subcategorias';

    protected $fillable = [
        'categoria_id',
        'nome',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categoria_id');
    }

    public function transacoes(): HasMany
    {
        return $this->hasMany(Transaction::class, 'subcategoria_id');
    }
}
