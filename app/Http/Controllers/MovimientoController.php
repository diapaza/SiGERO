<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movimiento\StoreMovimientoRequest;
use App\Http\Requests\Movimiento\UpdateMovimientoRequest;
use App\Models\Movimiento;
use App\Models\User;
use App\Services\MovimientoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MovimientoController extends BaseCrudController
{
    protected function modelClass(): string
    {
        return Movimiento::class;
    }

    protected function viewPath(): string
    {
        return 'Movimientos';
    }

    protected function routePrefix(): string
    {
        return 'movimientos';
    }

    protected function label(): string
    {
        return 'Movimiento';
    }

    protected function relations(): array
    {
        return ['objeto', 'user', 'registradoPor'];
    }

    public function index(Request $request): Response
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
        $data = $request->validated();
        $data['registrado_por'] = $request->user()->id;

        $service->create($data);

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
}
