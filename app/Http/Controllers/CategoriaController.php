<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categoria\StoreCategoriaRequest;
use App\Http\Requests\Categoria\UpdateCategoriaRequest;
use App\Models\Categoria;
use App\Services\CategoriaService;
use Illuminate\Http\RedirectResponse;

/**
 * Controlador CRUD de Categorías.
 *
 * Usa `BaseCrudController` para index/trashed y delega la lógica de negocio
 * en `CategoriaService`. No se permite eliminar una categoría que tenga
 * objetos asociados.
 */
class CategoriaController extends BaseCrudController
{
    /**
     * {@inheritDoc}
     */
    protected function modelClass(): string
    {
        return Categoria::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function viewPath(): string
    {
        return 'Categorias';
    }

    /**
     * {@inheritDoc}
     */
    protected function routePrefix(): string
    {
        return 'categorias';
    }

    /**
     * {@inheritDoc}
     */
    protected function label(): string
    {
        return 'Categoría';
    }

    /**
     * Crea una categoría a partir de los datos validados.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function store(StoreCategoriaRequest $request, CategoriaService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Actualiza una categoría existente.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria, CategoriaService $service): RedirectResponse
    {
        $service->update($categoria, $request->validated());

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Elimina (soft delete) una categoría si no tiene objetos asociados.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito o error.
     */
    public function destroy(Categoria $categoria, CategoriaService $service): RedirectResponse
    {
        $deleted = $service->delete($categoria);

        if (! $deleted) {
            return back()->with('error', 'No se puede eliminar una categoría que tiene objetos asociados.');
        }

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }

    /**
     * Restaura una categoría desde la papelera.
     *
     * @return RedirectResponse Redirección a la papelera con mensaje de éxito.
     */
    public function restore(Categoria $categoria, CategoriaService $service): RedirectResponse
    {
        $service->restore($categoria);

        return redirect()->route('categorias.trashed')->with('success', 'Categoría restaurada correctamente.');
    }
}
