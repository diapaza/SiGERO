<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Rol;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::with('role')->latest()->get();
        $roles = Rol::latest()->get();
        $trashedCount = User::onlyTrashed()->count();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'trashedCount' => $trashedCount,
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

    public function trashed(): Response
    {
        $users = User::with('role')->onlyTrashed()->latest('deleted_at')->get();

        return Inertia::render('Users/Trashed', [
            'users' => $users,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function restore(User $user, UserService $service): RedirectResponse
    {
        $service->restore($user);

        return redirect()->route('users.trashed')->with('success', 'Usuario restaurado correctamente.');
    }
}
