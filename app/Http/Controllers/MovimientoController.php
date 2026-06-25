<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movimiento\StoreMovimientoRequest;
use App\Http\Requests\Movimiento\UpdateMovimientoRequest;
use App\Models\Movimiento;
use App\Models\User;
use App\Services\MovimientoService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MovimientoController extends Controller
{
    public function index(): Response
    {
        $movimientos = Movimiento::with(['objeto', 'user', 'registradoPor'])->latest('fecha_hora')->get();
        $users = User::select('id', 'dni', 'nombres', 'apellidos', 'whatsapp_number')->get();

        return Inertia::render('Movimientos/Index', [
            'movimientos' => $movimientos,
            'users' => $users,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function store(StoreMovimientoRequest $request, MovimientoService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('movimientos.index')->with('success', 'Movimiento registrado correctamente.');
    }

    public function update(UpdateMovimientoRequest $request, Movimiento $movimiento, MovimientoService $service): RedirectResponse
    {
        $service->update($movimiento, $request->validated());

        return redirect()->route('movimientos.index')->with('success', 'Movimiento actualizado correctamente.');
    }

    public function destroy(Movimiento $movimiento, MovimientoService $service): RedirectResponse
    {
        $service->delete($movimiento);

        return redirect()->route('movimientos.index')->with('success', 'Movimiento eliminado correctamente.');
    }

    public function trashed(): Response
    {
        $movimientos = Movimiento::with(['objeto', 'user', 'registradoPor'])->onlyTrashed()->latest('deleted_at')->get();

        return Inertia::render('Movimientos/Trashed', [
            'movimientos' => $movimientos,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function restore(Movimiento $movimiento, MovimientoService $service): RedirectResponse
    {
        $service->restore($movimiento);

        return redirect()->route('movimientos.trashed')->with('success', 'Movimiento restaurado correctamente.');
    }
}
