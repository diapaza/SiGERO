<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categoria\StoreCategoriaRequest;
use App\Http\Requests\Categoria\UpdateCategoriaRequest;
use App\Models\Categoria;
use App\Services\CategoriaService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoriaController extends Controller
{
    public function index(): Response
    {
        $categorias = Categoria::latest()->get();
        $trashedCount = Categoria::onlyTrashed()->count();

        return Inertia::render('Categorias/Index', [
            'categorias' => $categorias,
            'trashedCount' => $trashedCount,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function store(StoreCategoriaRequest $request, CategoriaService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria, CategoriaService $service): RedirectResponse
    {
        $service->update($categoria, $request->validated());

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria, CategoriaService $service): RedirectResponse
    {
        $deleted = $service->delete($categoria);

        if (! $deleted) {
            return back()->with('error', 'No se puede eliminar una categoría que tiene objetos asociados.');
        }

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }

    public function trashed(): Response
    {
        $categorias = Categoria::onlyTrashed()->latest('deleted_at')->get();

        return Inertia::render('Categorias/Trashed', [
            'categorias' => $categorias,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function restore(Categoria $categoria, CategoriaService $service): RedirectResponse
    {
        $service->restore($categoria);

        return redirect()->route('categorias.trashed')->with('success', 'Categoría restaurada correctamente.');
    }
}
