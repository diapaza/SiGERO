<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;

/**
 * Controlador CRUD de Roles (Spatie Permission).
 *
 * Los roles **no** usan soft deletes (a diferencia de marcas/categorías/
 * objetos/usuarios). El listado incluye el conteo de usuarios por rol
 * (`users_count`). No se permite eliminar un rol que tenga usuarios asignados.
 */
class RoleController extends BaseCrudController
{
    /**
     * {@inheritDoc}
     */
    protected function modelClass(): string
    {
        return Role::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function viewPath(): string
    {
        return 'Roles';
    }

    /**
     * {@inheritDoc}
     */
    protected function routePrefix(): string
    {
        return 'roles';
    }

    /**
     * {@inheritDoc}
     */
    protected function label(): string
    {
        return 'Rol';
    }

    /**
     * Los roles no se eliminan de forma suave.
     *
     * {@inheritDoc}
     */
    protected function usesSoftDeletes(): bool
    {
        return false;
    }

    /**
     * Agrega el conteo de usuarios por rol al listado.
     *
     * {@inheritDoc}
     */
    protected function indexQuery(): ?Builder
    {
        return Role::query()->withCount('users');
    }

    /**
     * Crea un rol con guard `web`.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function store(StoreRoleRequest $request, RoleService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    /**
     * Actualiza el nombre de un rol.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function update(UpdateRoleRequest $request, Role $role, RoleService $service): RedirectResponse
    {
        $service->update($role, $request->validated());

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Elimina un rol si no tiene usuarios asignados.
     *
     * Antes de borrar, se desvinculan los permisos del rol (`detach`).
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito o error.
     */
    public function destroy(Role $role, RoleService $service): RedirectResponse
    {
        $deleted = $service->delete($role);

        if (! $deleted) {
            return back()->with('error', 'No se puede eliminar un rol que tiene usuarios asignados.');
        }

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
