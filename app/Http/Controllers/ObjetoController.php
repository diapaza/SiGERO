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
use Inertia\Inertia;
use Inertia\Response;

class ObjetoController extends Controller
{
    public function index(): Response
    {
        $objetos = Objeto::with(['marca', 'categoria'])->latest()->get();
        $marcas = Marca::latest()->get();
        $categorias = Categoria::latest()->get();
        $trashedCount = Objeto::onlyTrashed()->count();

        return Inertia::render('Objetos/Index', [
            'objetos' => $objetos,
            'marcas' => $marcas,
            'categorias' => $categorias,
            'trashedCount' => $trashedCount,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function store(StoreObjetoRequest $request, ObjetoService $service): RedirectResponse
    {
        $data = $request->validated();
        $data['codigo'] = Objeto::generarSiguienteCodigo();

        $service->create($data);

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

    public function uploadImage(Request $request, ImageService $imageService): JsonResponse
    {
        $request->validate([
            'foto' => ['required', 'image', 'max:512'],
        ]);

        $path = $imageService->process($request->file('foto'), 'objetos');

        return response()->json([
            'url' => asset('storage/' . $path),
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
