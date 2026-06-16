<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Rol;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Rol::latest()->get();
        $trashedCount = Rol::onlyTrashed()->count();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'trashedCount' => $trashedCount,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function store(StoreRoleRequest $request, RoleService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function update(UpdateRoleRequest $request, Rol $role, RoleService $service): RedirectResponse
    {
        $service->update($role, $request->validated());

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Rol $role, RoleService $service): RedirectResponse
    {
        $deleted = $service->delete($role);

        if (! $deleted) {
            return back()->with('error', 'No se puede eliminar un rol que tiene usuarios asignados.');
        }

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }

    public function trashed(): Response
    {
        $roles = Rol::onlyTrashed()->latest('deleted_at')->get();

        return Inertia::render('Roles/Trashed', [
            'roles' => $roles,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function restore(Rol $role, RoleService $service): RedirectResponse
    {
        $service->restore($role);

        return redirect()->route('roles.trashed')->with('success', 'Rol restaurado correctamente.');
    }
}
