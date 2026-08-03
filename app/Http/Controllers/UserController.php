<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends BaseCrudController
{
    protected function modelClass(): string
    {
        return User::class;
    }

    protected function viewPath(): string
    {
        return 'Users';
    }

    protected function routePrefix(): string
    {
        return 'users';
    }

    protected function label(): string
    {
        return 'Usuario';
    }

    protected function relations(): array
    {
        return ['roles'];
    }

    public function index(Request $request): Response
    {
        $users = User::with('roles')->latest()->get();
        $roles = Role::latest()->get();
        $trashedCount = User::onlyTrashed()->count();
        $allPermissions = Permission::orderBy('name')->get();

        $users->each(function ($user) {
            $user->setRelation('all_permissions', $user->getAllPermissions());
        });

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'trashedCount' => $trashedCount,
            'allPermissions' => $allPermissions,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function store(StoreUserRequest $request, UserService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function update(UpdateUserRequest $request, User $user, UserService $service): RedirectResponse
    {
        $service->update($user, $request->validated());

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user, UserService $service): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puede eliminar su propio usuario.');
        }

        $deleted = $service->delete($user);

        if (! $deleted) {
            return back()->with('error', 'No se puede eliminar un usuario que tiene movimientos registrados.');
        }

        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function restore(User $user, UserService $service): RedirectResponse
    {
        $service->restore($user);

        return redirect()->route('users.trashed')->with('success', 'Usuario restaurado correctamente.');
    }

    public function syncPermissions(User $user, UserService $service): RedirectResponse
    {
        $validated = request()->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $permissionNames = $validated['permissions'] ?? [];

        $service->syncPermissions($user, $permissionNames);

        return back()->with('success', 'Permisos actualizados correctamente.');
    }
}
