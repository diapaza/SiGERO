<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\Movimiento;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controlador del perfil del usuario autenticado.
 *
 * Permite ver/editar los datos personales (solo los administradores pueden
 * cambiar el username), cambiar la contraseña (validando la actual) y ver la
 * lista de objetos pendientes de devolución del usuario.
 */
class ProfileController extends Controller
{
    /**
     * Muestra el perfil del usuario con sus préstamos pendientes.
     *
     * Los pendientes son salidas del usuario sin un retorno posterior del
     * mismo objeto.
     *
     * Props que recibe la vista `Others/UserProfile`:
     * - `user`: usuario autenticado con sus roles cargados.
     * - `pendingReturns`: movimientos de salida activos con objeto (marca/categoría).
     *
     * @return Response Vista Inertia `Others/UserProfile`.
     */
    public function show(Request $request): Response
    {
        $user = $request->user();

        $pendingReturns = Movimiento::where('user_id', $user->id)
            ->where('tipo_movimiento', 'salida')
            ->whereNotIn('objeto_id', function ($query) use ($user) {
                $query->select('objeto_id')
                    ->from('movimientos')
                    ->where('user_id', $user->id)
                    ->where('tipo_movimiento', 'retorno');
            })
            ->with('objeto.marca', 'objeto.categoria')
            ->get();

        return Inertia::render('Others/UserProfile', [
            'user' => $user->load('roles'),
            'pendingReturns' => $pendingReturns,
        ]);
    }

    /**
     * Actualiza los datos personales del usuario autenticado.
     *
     * @return RedirectResponse Vuelve al perfil con mensaje de éxito.
     */
    public function update(UpdateProfileRequest $request, UserService $service): RedirectResponse
    {
        $service->updateProfile($request->user(), $request->validated());

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Actualiza la contraseña del usuario autenticado.
     *
     * @return RedirectResponse Vuelve al perfil con mensaje de éxito.
     */
    public function updatePassword(UpdatePasswordRequest $request, UserService $service): RedirectResponse
    {
        $service->updatePassword($request->user(), $request->validated());

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
