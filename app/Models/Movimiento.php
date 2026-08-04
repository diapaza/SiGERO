<?php

namespace App\Models;

use App\Enums\TipoMovimientoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Representa un movimiento de préstamo (salida o retorno) de un objeto.
 *
 * Cada movimiento vincula un objeto con el usuario responsable y registra
 * quién lo generó. Los movimientos usan soft deletes; `MovimientoService`
 * mantiene la consistencia del flag `disponible` del objeto ante cualquier
 * alta, baja o modificación.
 *
 * @property int $id
 * @property int $user_id Usuario responsable del objeto en ese momento.
 * @property int $objeto_id Objeto involucrado (inmutable al editar).
 * @property int $registrado_por Usuario que registró el movimiento.
 * @property TipoMovimientoEnum $tipo_movimiento `salida` | `retorno`.
 * @property Carbon $fecha_hora Fecha/hora del movimiento.
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Movimiento extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atributos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'objeto_id',
        'registrado_por',
        'tipo_movimiento',
        'fecha_hora',
    ];

    /**
     * Casts de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipo_movimiento' => TipoMovimientoEnum::class,
            'fecha_hora' => 'datetime',
        ];
    }

    /**
     * Objeto involucrado en el movimiento.
     */
    public function objeto(): BelongsTo
    {
        return $this->belongsTo(Objeto::class);
    }

    /**
     * Usuario responsable del objeto en ese movimiento.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuario que registró el movimiento.
     */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
