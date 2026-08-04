<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Representa un objeto/equipo del inventario.
 *
 * El campo `disponible` es **derivado** del historial de movimientos y nunca
 * debe asignarse manualmente: `MovimientoService` lo recalcula al crear,
 * actualizar, eliminar o restaurar un movimiento. Los objetos usan soft
 * deletes y el código es único (4 o 12 dígitos).
 *
 * @property int $id
 * @property string $codigo Código único (4 o 12 dígitos).
 * @property string $nombre Nombre del objeto.
 * @property string|null $modelo Modelo del objeto.
 * @property string|null $descripcion Descripción libre.
 * @property int|null $marca_id FK a `marcas`.
 * @property int|null $categoria_id FK a `categorias`.
 * @property string|null $foto Ruta relativa en `storage/app/public/objetos`.
 * @property string|null $serie Número de serie.
 * @property bool $disponible Flag derivado: `true` si el último movimiento no es una salida.
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Objeto disponibles()
 * @method static \Illuminate\Database\Eloquent\Builder|Objeto prestados()
 */
class Objeto extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atributos asignables en masa.
     *
     * @var list<string>
     */
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

    /**
     * Casts de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'disponible' => 'boolean',
        ];
    }

    /**
     * Todos los movimientos del objeto (incluye los soft-deleted).
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }

    /**
     * Scope: objetos disponibles (`disponible = true`).
     */
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', 1);
    }

    /**
     * Scope: objetos prestados (`disponible = false`).
     */
    public function scopePrestados($query)
    {
        return $query->where('disponible', 0);
    }

    /**
     * Estadísticas globales para el dashboard.
     *
     * @return array{total: int, disponibles: int, prestados: int, eliminados: int}
     */
    public static function estadisticas()
    {
        return [
            'total' => self::count(),
            'disponibles' => self::disponibles()->count(),
            'prestados' => self::prestados()->count(),
            'eliminados' => self::onlyTrashed()->count(),
        ];
    }

    /**
     * Obtiene un objeto activo por su código o lanza una excepción.
     *
     * @param  string  $codigo  Código del objeto.
     * @return Objeto
     */
    public static function activoPorCodigo($codigo)
    {
        return self::where('codigo', $codigo)->firstOrFail();
    }

    /**
     * Genera el siguiente código de 4 dígitos (0001..9999).
     *
     * Bloquea la fila del código máximo dentro de una transacción para que
     * dos creaciones simultáneas no generen el mismo código.
     */
    public static function generarSiguienteCodigo(): string
    {
        return DB::transaction(function () {
            $ultimoCodigo = self::withTrashed()
                ->whereRaw('LENGTH(codigo) = 4')
                ->where('codigo', 'REGEXP', '^[0-9]{4}$')
                ->orderByRaw('CAST(codigo AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->value('codigo');

            $numero = $ultimoCodigo ? (int) $ultimoCodigo : 0;

            return str_pad($numero + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Último movimiento del objeto por `fecha_hora`.
     */
    public function ultimoMovimiento(): HasOne
    {
        return $this->hasOne(Movimiento::class)->latestOfMany('fecha_hora');
    }

    /**
     * Última salida registrada (movimiento activo) del objeto.
     *
     * Se usa en el dashboard y en las notificaciones de devolución vencida.
     */
    public function movimientoActivo(): HasOne
    {
        return $this->hasOne(Movimiento::class)
            ->where('tipo_movimiento', 'salida')
            ->latestOfMany('fecha_hora');
    }

    /**
     * Marca a la que pertenece el objeto.
     */
    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    /**
     * Categoría a la que pertenece el objeto.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
