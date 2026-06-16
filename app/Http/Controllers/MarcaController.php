<?php

namespace App\Http\Controllers;

use App\Http\Requests\Marca\StoreMarcaRequest;
use App\Http\Requests\Marca\UpdateMarcaRequest;
use App\Models\Marca;
use App\Services\MarcaService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MarcaController extends Controller
{
    public function index(): Response
    {
        $marcas = Marca::latest()->get();
        $trashedCount = Marca::onlyTrashed()->count();

        return Inertia::render('Marcas/Index', [
            'marcas' => $marcas,
            'trashedCount' => $trashedCount,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function store(StoreMarcaRequest $request, MarcaService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('marcas.index')->with('success', 'Marca creada correctamente.');
    }

    public function update(UpdateMarcaRequest $request, Marca $marca, MarcaService $service): RedirectResponse
    {
        $service->update($marca, $request->validated());

        return redirect()->route('marcas.index')->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(Marca $marca, MarcaService $service): RedirectResponse
    {
        $deleted = $service->delete($marca);

        if (! $deleted) {
            return back()->with('error', 'No se puede eliminar una marca que tiene objetos asociados.');
        }

        return redirect()->route('marcas.index')->with('success', 'Marca eliminada correctamente.');
    }

    public function trashed(): Response
    {
        $marcas = Marca::onlyTrashed()->latest('deleted_at')->get();

        return Inertia::render('Marcas/Trashed', [
            'marcas' => $marcas,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function restore(Marca $marca, MarcaService $service): RedirectResponse
    {
        $service->restore($marca);

        return redirect()->route('marcas.trashed')->with('success', 'Marca restaurada correctamente.');
    }
}
