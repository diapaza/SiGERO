<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Objeto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'nombre',
        'modelo',
        'descripcion',
        'marca_id',
        'categoria_id',
        'foto',
        'serie',
        'disponible',
    ];

    protected function casts(): array
    {
        return [
            'disponible' => 'boolean',
        ];
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }

    // Objetos disponibles
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', 1);
    }

    // Objetos prestados
    public function scopePrestados($query)
    {
        return $query->where('disponible', 0);
    }

    public static function estadisticas()
    {
        return [
            'total' => self::count(),
            'disponibles' => self::disponibles()->count(),
            'prestados' => self::prestados()->count(),
            'eliminados' => self::onlyTrashed()->count(),
        ];
    }

    public static function activoPorCodigo($codigo)
    {
        return self::where('codigo', $codigo)->firstOrFail();
    }

    public static function generarSiguienteCodigo(): string
    {
        $ultimoCodigo = self::withTrashed()
            ->whereRaw('LENGTH(codigo) = 4')
            ->where('codigo', 'REGEXP', '^[0-9]{4}$')
            ->orderByRaw('CAST(codigo AS UNSIGNED) DESC')
            ->value('codigo');

        if (! $ultimoCodigo) {
            return '0001';
        }

        $numero = (int) $ultimoCodigo;
        $siguiente = $numero + 1;

        return str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }

    public function ultimoMovimiento()
    {
        return $this->hasOne(Movimiento::class)->latestOfMany('fecha_hora');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
