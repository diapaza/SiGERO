<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Rol de Spatie Permission.
 *
 * A diferencia del resto de entidades, los roles **no** usan soft deletes.
 * `users_count` se agrega en el listado vía `withCount`.
 *
 * @property int $id
 * @property string $name Nombre del rol.
 * @property string $guard_name Guard (por defecto `web`).
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Role extends SpatieRole
{
    use HasFactory;

    /**
     * Atributos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'guard_name',
    ];
}
