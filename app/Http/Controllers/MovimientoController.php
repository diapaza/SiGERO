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

/**
 * Controlador de Movimientos (salidas y retornos).
 *
 * Registra, edita y elimina movimientos de préstamo. La lógica de invariantes
 * (no doble salida, retorno solo si está prestado) y el recálculo de la
 * disponibilidad del objeto viven en `MovimientoService`. El campo
 * `registrado_por` siempre se fuerza al usuario autenticado en el servidor.
 */
class MovimientoController extends BaseCrudController
{
    /**
     * {@inheritDoc}
     */
    protected function modelClass(): string
    {
        return Movimiento::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function viewPath(): string
    {
        return 'Movimientos';
    }

    /**
     * {@inheritDoc}
     */
    protected function routePrefix(): string
    {
        return 'movimientos';
    }

    /**
     * {@inheritDoc}
     */
    protected function label(): string
    {
        return 'Movimiento';
    }

    /**
     * Carga objeto, usuario responsable y quien registró cada movimiento.
     *
     * {@inheritDoc}
     */
    protected function relations(): array
    {
        return ['objeto', 'user', 'registradoPor'];
    }

    /**
     * Renderiza el módulo de movimientos con su historial.
     *
     * Props que recibe la vista `Movimientos/Index`:
     * - `movimientos`: historial completo con objeto, user y registrado_por.
     * - `users`: personas disponibles para asignar en el modal de edición.
     * - `flash.success` / `flash.error`: mensajes de sesión.
     *
     * @return Response Vista Inertia `Movimientos/Index`.
     */
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

    /**
     * Registra un movimiento de salida o retorno.
     *
     * El `registrado_por` se asigna en el servidor con el usuario autenticado.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function store(StoreMovimientoRequest $request, MovimientoService $service): RedirectResponse
    {
        $data = $request->validated();
        $data['registrado_por'] = $request->user()->id;

        $service->create($data);

        return redirect()->route('movimientos.index')->with('success', 'Movimiento registrado correctamente.');
    }

    /**
     * Actualiza un movimiento (tipo, responsable o fecha).
     *
     * El `objeto_id` es inmutable (lo ignora `MovimientoService::update`).
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function update(UpdateMovimientoRequest $request, Movimiento $movimiento, MovimientoService $service): RedirectResponse
    {
        $service->update($movimiento, $request->validated());

        return redirect()->route('movimientos.index')->with('success', 'Movimiento actualizado correctamente.');
    }

    /**
     * Elimina (soft delete) un movimiento y recalcula la disponibilidad del objeto.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function destroy(Movimiento $movimiento, MovimientoService $service): RedirectResponse
    {
        $service->delete($movimiento);

        return redirect()->route('movimientos.index')->with('success', 'Movimiento eliminado correctamente.');
    }
}
