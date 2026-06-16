<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marca extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
    ];

    public function objetos(): HasMany
    {
        return $this->hasMany(Objeto::class);
    }
}
