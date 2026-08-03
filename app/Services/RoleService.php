<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

readonly class RoleService extends BaseCrudService
{
    public function create(array $data): Role
    {
        $data['guard_name'] = $data['guard_name'] ?? 'web';

        return parent::create($data);
    }

    public function delete(Model $entity): bool
    {
        if ($this->hasDependents($entity)) {
            return false;
        }

        /** @var Role $role */
        $role = $entity;
        $role->permissions()->detach();

        return $role->delete();
    }

    protected function hasDependents(Model $entity): bool
    {
        /** @var Role $role */
        $role = $entity;

        return $role->users()->count() > 0;
    }
}
