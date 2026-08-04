<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Permiso de Spatie Permission.
 *
 * Define los 12 permisos del sistema (ver `Database\Seeders\PermissionSeeder`)
 * que se asignan a roles o directamente a usuarios.
 *
 * @property int $id
 * @property string $name Nombre del permiso.
 * @property string $guard_name Guard (por defecto `web`).
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Permission extends SpatiePermission
{
    use HasFactory;

    /**
     * Tabla asociada (Spatie ya la define; se explicita por claridad).
     *
     * @var string
     */
    protected $table = 'permissions';

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
