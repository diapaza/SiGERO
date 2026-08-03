<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

readonly class UserService extends BaseCrudService
{
    public function __construct(
        private User $model,
        private NotificationService $notifications,
    ) {
        parent::__construct($model);
    }

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

    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    public function updatePassword(User $user, array $data): void
    {
        $user->update([
            'password' => $data['password'],
        ]);
    }

    public function syncPermissions(User $user, array $permissionNames): void
    {
        $user->syncPermissions($permissionNames);

        $this->notifications->permisosActualizados($user, auth()->user());
    }
}
