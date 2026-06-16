<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre',
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Ya existe un rol con ese nombre.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
        ]);

        Rol::create($validated);

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function update(Request $request, Rol $role)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre,' . $role->id,
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Ya existe un rol con ese nombre.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
        ]);

        $role->update($validated);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Rol $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un rol que tiene usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }

    public function trashed()
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

    public function restore(Rol $role)
    {
        $role->restore();

        return redirect()->route('roles.trashed')->with('success', 'Rol restaurado correctamente.');
    }
}
