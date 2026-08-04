<?php

namespace App\Http\Controllers;

use App\Http\Requests\Objeto\StoreObjetoRequest;
use App\Http\Requests\Objeto\UpdateObjetoRequest;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Objeto;
use App\Services\ImageService;
use App\Services\ObjetoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Controlador CRUD de Objetos.
 *
 * Además del CRUD base (con marca/categoría como extras del formulario),
 * gestiona la subida/eliminación de fotos (`upload-image` / `delete-image`).
 * El código es inmutable al editar y, si se deja vacío al crear, se genera
 * automáticamente. No se permite eliminar un objeto con movimientos.
 */
class ObjetoController extends BaseCrudController
{
    /**
     * {@inheritDoc}
     */
    protected function modelClass(): string
    {
        return Objeto::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function viewPath(): string
    {
        return 'Objetos';
    }

    /**
     * {@inheritDoc}
     */
    protected function routePrefix(): string
    {
        return 'objetos';
    }

    /**
     * {@inheritDoc}
     */
    protected function label(): string
    {
        return 'Objeto';
    }

    /**
     * Carga marca y categoría con cada objeto del listado.
     *
     * {@inheritDoc}
     */
    protected function relations(): array
    {
        return ['marca', 'categoria'];
    }

    /**
     * Provee las marcas y categorías para los selectores del formulario.
     *
     * {@inheritDoc}
     */
    protected function indexExtras(Request $request): array
    {
        return [
            'marcas' => Marca::latest()->get(),
            'categorias' => Categoria::latest()->get(),
        ];
    }

    /**
     * Crea un objeto (el código vacío se auto-genera en `ObjetoService`).
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function store(StoreObjetoRequest $request, ObjetoService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('objetos.index')->with('success', 'Objeto creado correctamente.');
    }

    /**
     * Actualiza un objeto (el código no puede cambiarse).
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito.
     */
    public function update(UpdateObjetoRequest $request, Objeto $objeto, ObjetoService $service): RedirectResponse
    {
        $data = $request->validated();
        unset($data['codigo']);

        $service->update($objeto, $data);

        return redirect()->route('objetos.index')->with('success', 'Objeto actualizado correctamente.');
    }

    /**
     * Elimina (soft delete) un objeto si no tiene movimientos registrados.
     *
     * @return RedirectResponse Redirección al listado con mensaje de éxito o error.
     */
    public function destroy(Objeto $objeto, ObjetoService $service): RedirectResponse
    {
        $deleted = $service->delete($objeto);

        if (! $deleted) {
            return back()->with('error', 'No se puede eliminar un objeto que tiene movimientos registrados.');
        }

        return redirect()->route('objetos.index')->with('success', 'Objeto eliminado correctamente.');
    }

    /**
     * Restaura un objeto desde la papelera.
     *
     * @return RedirectResponse Redirección a la papelera con mensaje de éxito.
     */
    public function restore(Objeto $objeto, ObjetoService $service): RedirectResponse
    {
        $service->restore($objeto);

        return redirect()->route('objetos.trashed')->with('success', 'Objeto restaurado correctamente.');
    }

    /**
     * Sube la foto de un objeto (endpoint interno usado por el dropzone).
     *
     * Procesa la imagen (redimensión, JPEG) y devuelve la ruta relativa
     * almacenada. La imagen se renombra a `{codigo}.jpg` al guardar el objeto.
     *
     * @return JsonResponse Con `url` (pública) y `path` (relativa) de la imagen.
     */
    public function uploadImage(Request $request, ImageService $imageService): JsonResponse
    {
        $request->validate([
            'foto' => ['required', 'image', 'max:512'],
        ]);

        $path = $imageService->process($request->file('foto'), 'objetos');

        return response()->json([
            'url' => asset('storage/'.$path),
            'path' => $path,
        ]);
    }

    /**
     * Elimina una imagen temporal subida (endpoint interno del dropzone).
     *
     * La ruta se valida contra path traversal en `ImageService::delete`.
     *
     * @return JsonResponse Siempre `{ success: true }`.
     */
    public function deleteImage(Request $request, ImageService $imageService): JsonResponse
    {
        $request->validate(['path' => ['required', 'string']]);

        $imageService->delete($request->path);

        return response()->json(['success' => true]);
    }
}
