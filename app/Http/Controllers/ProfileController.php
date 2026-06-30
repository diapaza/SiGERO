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

class ProfileController extends Controller
{
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
            'user' => $user->load('role'),
            'pendingReturns' => $pendingReturns,
        ]);
    }

    public function update(UpdateProfileRequest $request, UserService $service): RedirectResponse
    {
        $service->updateProfile($request->user(), $request->validated());

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(UpdatePasswordRequest $request, UserService $service): RedirectResponse
    {
        $service->updatePassword($request->user(), $request->validated());

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
