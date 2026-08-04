<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Marca de objetos.
 *
 * Usa soft deletes. No se puede eliminar una marca que tenga objetos
 * asociados (lo valida `MarcaService`).
 *
 * @property int $id
 * @property string $nombre Nombre de la marca (único).
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Marca extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atributos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
    ];

    /**
     * Objetos que pertenecen a esta marca.
     */
    public function objetos(): HasMany
    {
        return $this->hasMany(Objeto::class);
    }
}
