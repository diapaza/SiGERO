<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Servicio de dominio de usuarios.
 *
 * Gestiona el CRUD de usuarios, la asignación de roles, la sincronización de
 * permisos directos, la contraseña y el perfil. Dispara las notificaciones de
 * cuenta creada y de permisos/roles actualizados cuando corresponde.
 */
readonly class UserService extends BaseCrudService
{
    public function __construct(
        private User $model,
        private NotificationService $notifications,
    ) {
        parent::__construct($model);
    }

    /**
     * Crea un usuario, asigna sus roles y notifica la creación de la cuenta.
     *
     * @param  array<string, mixed>  $data  Datos validados (incluye `roles` opcional).
     */
    public function create(array $data): User
    {
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $user = parent::create($data);

        if (! empty($roles)) {
            $user->syncRoles($roles);
        }

        $this->notifications->cuentaCreada($user);

        return $user;
    }

    /**
     * Actualiza los datos, roles y (opcionalmente) la contraseña de un usuario.
     *
     * Si los roles cambian, notifica al usuario afectado.
     *
     * @param  array<string, mixed>  $data
     * @return Model|User Usuario actualizado con roles recargados.
     */
    public function update(Model $entity, array $data): Model
    {
        /** @var User $user */
        $user = $entity;
        $roles = $data['roles'] ?? null;
        unset($data['roles']);

        $rolesAnteriores = $user->roles->pluck('name')->sort()->values();

        parent::update($user, $data);

        if ($roles !== null) {
            $user->syncRoles($roles);
        }

        $rolesNuevos = $user->roles->pluck('name')->sort()->values();

        $rolesCambiaron = $rolesAnteriores->diff($rolesNuevos)->isNotEmpty()
            || $rolesNuevos->diff($rolesAnteriores)->isNotEmpty();

        if ($roles !== null && $rolesCambiaron) {
            $this->notifications->permisosActualizados($user, auth()->user());
        }

        return $user->fresh(['roles']);
    }

    /**
     * Elimina (soft delete) un usuario si no es el actual ni tiene movimientos.
     *
     * Antes de eliminar se desvinculan sus roles.
     */
    public function delete(Model $entity): bool
    {
        /** @var User $user */
        $user = $entity;

        if ($user->id === auth()->id()) {
            return false;
        }

        if ($user->movimientos()->count() > 0) {
            return false;
        }

        $user->syncRoles([]);

        return $user->delete();
    }

    /**
     * Actualiza los datos personales del propio usuario (perfil).
     *
     * @param  array<string, mixed>  $data
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    /**
     * Actualiza la contraseña de un usuario (se guarda hasheada por el cast).
     *
     * @param  array<string, mixed>  $data  Debe incluir `password`.
     */
    public function updatePassword(User $user, array $data): void
    {
        $user->update([
            'password' => $data['password'],
        ]);
    }

    /**
     * Reemplaza los permisos directos del usuario y le notifica.
     *
     * @param  array<int, string>  $permissionNames  Nombres de permisos directos.
     */
    public function syncPermissions(User $user, array $permissionNames): void
    {
        $user->syncPermissions($permissionNames);

        $this->notifications->permisosActualizados($user, auth()->user());
    }
}
