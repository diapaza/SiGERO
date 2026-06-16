<?php

namespace App\Http\Controllers;

use App\Http\Requests\Objeto\StoreObjetoRequest;
use App\Http\Requests\Objeto\UpdateObjetoRequest;
use App\Models\Objeto;
use App\Services\ObjetoService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ObjetoController extends Controller
{
    public function index(): Response
    {
        $objetos = Objeto::with(['marca', 'categoria'])->latest()->get();
        $trashedCount = Objeto::onlyTrashed()->count();

        return Inertia::render('Objetos/Index', [
            'objetos' => $objetos,
            'trashedCount' => $trashedCount,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function store(StoreObjetoRequest $request, ObjetoService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('objetos.index')->with('success', 'Objeto creado correctamente.');
    }

    public function update(UpdateObjetoRequest $request, Objeto $objeto, ObjetoService $service): RedirectResponse
    {
        $service->update($objeto, $request->validated());

        return redirect()->route('objetos.index')->with('success', 'Objeto actualizado correctamente.');
    }

    public function destroy(Objeto $objeto, ObjetoService $service): RedirectResponse
    {
        $deleted = $service->delete($objeto);

        if (! $deleted) {
            return back()->with('error', 'No se puede eliminar un objeto que tiene movimientos registrados.');
        }

        return redirect()->route('objetos.index')->with('success', 'Objeto eliminado correctamente.');
    }

    public function trashed(): Response
    {
        $objetos = Objeto::with(['marca', 'categoria'])->onlyTrashed()->latest('deleted_at')->get();

        return Inertia::render('Objetos/Trashed', [
            'objetos' => $objetos,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function restore(Objeto $objeto, ObjetoService $service): RedirectResponse
    {
        $service->restore($objeto);

        return redirect()->route('objetos.trashed')->with('success', 'Objeto restaurado correctamente.');
    }
}
