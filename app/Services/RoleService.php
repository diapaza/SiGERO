<?php

namespace App\Services;

use App\Models\Rol;

readonly class RoleService
{
    public function __construct(
        private Rol $model,
    ) {}

    public function create(array $data): Rol
    {
        return $this->model->create($data);
    }

    public function update(Rol $role, array $data): Rol
    {
        $role->update($data);

        return $role->fresh();
    }

    public function delete(Rol $role): bool
    {
        if ($role->users()->count() > 0) {
            return false;
        }

        return $role->delete();
    }

    public function restore(Rol $role): bool
    {
        return $role->restore();
    }
}
