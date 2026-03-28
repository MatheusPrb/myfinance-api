<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasUuids;

    protected $table = 'categorias';

    protected $fillable = [
        'nome',
    ];

    public function subcategorias(): HasMany
    {
        return $this->hasMany(Subcategory::class, 'categoria_id');
    }

    public function transacoes(): HasMany
    {
        return $this->hasMany(Transaction::class, 'categoria_id');
    }
}
