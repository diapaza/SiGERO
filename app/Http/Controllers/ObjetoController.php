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

class ObjetoController extends BaseCrudController
{
    protected function modelClass(): string
    {
        return Objeto::class;
    }

    protected function viewPath(): string
    {
        return 'Objetos';
    }

    protected function routePrefix(): string
    {
        return 'objetos';
    }

    protected function label(): string
    {
        return 'Objeto';
    }

    protected function relations(): array
    {
        return ['marca', 'categoria'];
    }

    protected function indexExtras(Request $request): array
    {
        return [
            'marcas' => Marca::latest()->get(),
            'categorias' => Categoria::latest()->get(),
        ];
    }

    public function store(StoreObjetoRequest $request, ObjetoService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('objetos.index')->with('success', 'Objeto creado correctamente.');
    }

    public function update(UpdateObjetoRequest $request, Objeto $objeto, ObjetoService $service): RedirectResponse
    {
        $data = $request->validated();
        unset($data['codigo']);

        $service->update($objeto, $data);

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

    public function restore(Objeto $objeto, ObjetoService $service): RedirectResponse
    {
        $service->restore($objeto);

        return redirect()->route('objetos.trashed')->with('success', 'Objeto restaurado correctamente.');
    }

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

    public function deleteImage(Request $request, ImageService $imageService): JsonResponse
    {
        $request->validate(['path' => ['required', 'string']]);

        $imageService->delete($request->path);

        return response()->json(['success' => true]);
    }
}
