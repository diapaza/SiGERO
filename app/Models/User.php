<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * Usuario del sistema (autenticación + roles/permisos Spatie).
 *
 * Los usuarios usan soft deletes. `name` es un accesor calculado a partir de
 * `nombres` y `apellidos`. Se autentica por `username`, no por email.
 *
 * @property int $id
 * @property string $username Nombre de usuario único.
 * @property string $dni DNI de 8 dígitos único.
 * @property string $nombres
 * @property string $apellidos
 * @property string|null $whatsapp_number
 * @property string $password Hash de la contraseña (cast `hashed`).
 * @property-read string $name Nombre completo (accesor `nombres + apellidos`).
 * @property string|null $remember_token
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Role> $roles Roles asignados.
 * @property-read Collection<int, Permission> $permissions Permisos directos.
 * @property-read Collection<int, Movimiento> $movimientos
 * @property-read Collection<int, Movimiento> $movimientosRegistrados
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'dni',
        'nombres',
        'apellidos',
        'whatsapp_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos calculados que se serializan.
     *
     * @var list<string>
     */
    protected $appends = ['name'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Nombre completo del usuario (`nombres` + `apellidos`).
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->nombres} {$this->apellidos}"),
        );
    }

    /**
     * Movimientos de préstamo donde el usuario es el responsable.
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class, 'user_id');
    }

    /**
     * Movimientos registrados por el usuario (autoría).
     */
    public function movimientosRegistrados(): HasMany
    {
        return $this->hasMany(Movimiento::class, 'registrado_por');
    }
}
