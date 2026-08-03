<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categoria\StoreCategoriaRequest;
use App\Http\Requests\Categoria\UpdateCategoriaRequest;
use App\Models\Categoria;
use App\Services\CategoriaService;
use Illuminate\Http\RedirectResponse;

class CategoriaController extends BaseCrudController
{
    protected function modelClass(): string
    {
        return Categoria::class;
    }

    protected function viewPath(): string
    {
        return 'Categorias';
    }

    protected function routePrefix(): string
    {
        return 'categorias';
    }

    protected function label(): string
    {
        return 'Categoría';
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

    public function restore(Categoria $categoria, CategoriaService $service): RedirectResponse
    {
        $service->restore($categoria);

        return redirect()->route('categorias.trashed')->with('success', 'Categoría restaurada correctamente.');
    }
}
