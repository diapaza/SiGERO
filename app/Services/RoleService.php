<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

/**
 * Servicio de dominio de roles (Spatie Permission).
 *
 * Crea roles con guard `web` por defecto, y al eliminar un rol desvincula sus
 * permisos. No permite eliminar un rol que tenga usuarios asignados.
 */
readonly class RoleService extends BaseCrudService
{
    /**
     * Crea un rol aplicando `guard_name = web` si no se especifica.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Role
    {
        $data['guard_name'] = $data['guard_name'] ?? 'web';

        return parent::create($data);
    }

    /**
     * Elimina un rol desvinculando antes sus permisos.
     */
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

    /**
     * Un rol no puede eliminarse si tiene usuarios asignados.
     */
    protected function hasDependents(Model $entity): bool
    {
        /** @var Role $role */
        $role = $entity;

        return $role->users()->count() > 0;
    }
}
