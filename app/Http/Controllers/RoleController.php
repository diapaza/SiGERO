<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;

class RoleController extends BaseCrudController
{
    protected function modelClass(): string
    {
        return Role::class;
    }

    protected function viewPath(): string
    {
        return 'Roles';
    }

    protected function routePrefix(): string
    {
        return 'roles';
    }

    protected function label(): string
    {
        return 'Rol';
    }

    protected function usesSoftDeletes(): bool
    {
        return false;
    }

    protected function indexQuery(): ?Builder
    {
        return Role::query()->withCount('users');
    }

    public function store(StoreRoleRequest $request, RoleService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function update(UpdateRoleRequest $request, Role $role, RoleService $service): RedirectResponse
    {
        $service->update($role, $request->validated());

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role, RoleService $service): RedirectResponse
    {
        $deleted = $service->delete($role);

        if (! $deleted) {
            return back()->with('error', 'No se puede eliminar un rol que tiene usuarios asignados.');
        }

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
