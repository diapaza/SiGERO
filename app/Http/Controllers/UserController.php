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

/**
 * Controlador CRUD de Usuarios.
 *
 * Gestiona usuarios, sus roles y sus permisos directos (Spatie Permission).
 * El listado expone los permisos efectivos y los derivados de rol de cada
 * usuario para que el modal de permisos solo manipule los directos. No se
 * puede eliminar el propio usuario ni uno con movimientos registrados.
 */
class UserController extends BaseCrudController
{
    /**
     * {@inheritDoc}
     */
    protected function modelClass(): string
    {
        return User::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function viewPath(): string
    {
        return 'Users';
    }

    /**
     * {@inheritDoc}
     */
    protected function routePrefix(): string
    {
        return 'users';
    }

    /**
     * {@inheritDoc}
     */
    protected function label(): string
    {
        return 'Usuario';
    }

    /**
     * Carga los roles (con sus permisos) de cada usuario del listado.
     *
     * {@inheritDoc}
     */
    protected function relations(): array
    {
        return ['roles'];
    }

    /**
     * Renderiza el módulo de usuarios.
     *
     * Props que recibe la vista `Users/Index`:
     * - `users`: usuarios con roles, `all_permissions` (efectivos) y `role_permissions`.
     * - `roles`: roles disponibles para asignar.
     * - `trashedCount`: usuarios en papelera.
     * - `allPermissions`: todos los permisos del sistema (para el modal).
     * - `flash.success` / `flash.error`: mensajes de sesión.
     *
     * @return Response Vista Inertia `Users/Index`.
     */
    public function index(Request $request): Response
    {
        $users = User::with('roles.permissions')->latest()->get();
        $roles = Role::latest()->get();
        $trashedCount = User::onlyTrashed()->count();
        $allPermissions = Permission::orderBy('name')->get();

        $users->each(function ($user) {
            $user->setRelation('all_permissions', $user->getAllPermissions());
            $user->setRelation('role_permissions', $user->roles->flatMap->permissions->pluck('name')->unique()->values());
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

    /**
     * Crea un usuario, le asigna roles y le notifica su cuenta.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function store(StoreUserRequest $request, UserService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Actualiza los datos, roles y contraseña (si se envía) de un usuario.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function update(UpdateUserRequest $request, User $user, UserService $service): RedirectResponse
    {
        $service->update($user, $request->validated());

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina (soft delete) un usuario que no sea el propio y sin movimientos.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito o error.
     */
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

    /**
     * Restaura un usuario desde la papelera.
     *
     * @return RedirectResponse Redirección a la papelera con mensaje de éxito.
     */
    public function restore(User $user, UserService $service): RedirectResponse
    {
        $service->restore($user);

        return redirect()->route('users.trashed')->with('success', 'Usuario restaurado correctamente.');
    }

    /**
     * Sincroniza los permisos directos de un usuario.
     *
     * Solo gestiona permisos directos (los del rol no se tocan). Notifica al
     * usuario afectado.
     *
     * @return RedirectResponse Vuelve a la página anterior con mensaje de éxito.
     */
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
