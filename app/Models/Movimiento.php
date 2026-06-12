<?php

namespace App\Models;

use App\Enums\TipoMovimientoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'objeto_id',
        'registrado_por',
        'tipo_movimiento',
        'fecha_hora',
    ];

    protected function casts(): array
    {
        return [
            'tipo_movimiento' => TipoMovimientoEnum::class,
            'fecha_hora' => 'datetime',
        ];
    }

    public function objeto(): BelongsTo
    {
        return $this->belongsTo(Objeto::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
