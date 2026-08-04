<?php

namespace App\Http\Controllers;

use App\Http\Requests\Marca\StoreMarcaRequest;
use App\Http\Requests\Marca\UpdateMarcaRequest;
use App\Models\Marca;
use App\Services\MarcaService;
use Illuminate\Http\RedirectResponse;

/**
 * Controlador CRUD de Marcas.
 *
 * Usa `BaseCrudController` para index/trashed y delega la lógica de negocio
 * en `MarcaService`. No se permite eliminar una marca que tenga objetos
 * asociados.
 */
class MarcaController extends BaseCrudController
{
    /**
     * {@inheritDoc}
     */
    protected function modelClass(): string
    {
        return Marca::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function viewPath(): string
    {
        return 'Marcas';
    }

    /**
     * {@inheritDoc}
     */
    protected function routePrefix(): string
    {
        return 'marcas';
    }

    /**
     * {@inheritDoc}
     */
    protected function label(): string
    {
        return 'Marca';
    }

    /**
     * Crea una marca a partir de los datos validados.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function store(StoreMarcaRequest $request, MarcaService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('marcas.index')->with('success', 'Marca creada correctamente.');
    }

    /**
     * Actualiza una marca existente.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function update(UpdateMarcaRequest $request, Marca $marca, MarcaService $service): RedirectResponse
    {
        $service->update($marca, $request->validated());

        return redirect()->route('marcas.index')->with('success', 'Marca actualizada correctamente.');
    }

    /**
     * Elimina (soft delete) una marca si no tiene objetos asociados.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito o error.
     */
    public function destroy(Marca $marca, MarcaService $service): RedirectResponse
    {
        $deleted = $service->delete($marca);

        if (! $deleted) {
            return back()->with('error', 'No se puede eliminar una marca que tiene objetos asociados.');
        }

        return redirect()->route('marcas.index')->with('success', 'Marca eliminada correctamente.');
    }

    /**
     * Restaura una marca desde la papelera.
     *
     * @return RedirectResponse Redirección a la papelera con mensaje de éxito.
     */
    public function restore(Marca $marca, MarcaService $service): RedirectResponse
    {
        $service->restore($marca);

        return redirect()->route('marcas.trashed')->with('success', 'Marca restaurada correctamente.');
    }
}
