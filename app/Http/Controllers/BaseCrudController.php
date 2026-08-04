<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controlador base para CRUD simples (Categorías, Marcas y otros sin lógica
 * especial). Ofrece las acciones `index` y `trashed` y un conjunto de hooks
 * que las subclases sobrescriben para configurar el modelo, la vista, las
 * relaciones y los extras que recibe cada página.
 *
 * Las entidades con reglas de negocio particulares (Objetos, Movimientos,
 * Usuarios, Roles) sobrescriben estos hooks o implementan sus propias acciones.
 */
abstract class BaseCrudController extends Controller
{
    /**
     * Devuelve el FQCN del modelo gestionado por el CRUD.
     */
    abstract protected function modelClass(): string;

    /**
     * Ruta relativa de la carpeta de vistas bajo `resources/js/views` (p. ej. `Categorias`).
     */
    abstract protected function viewPath(): string;

    /**
     * Prefijo de nombre de ruta (p. ej. `categorias`) y clave de props del listado.
     */
    abstract protected function routePrefix(): string;

    /**
     * Etiqueta singular en español usada en mensajes y confirmaciones (p. ej. `Categoría`).
     */
    abstract protected function label(): string;

    /**
     * Relaciones a cargar con eager-loading en index y trashed.
     *
     * @return array<int, string>
     */
    protected function relations(): array
    {
        return [];
    }

    /**
     * Indica si el modelo usa SoftDeletes (controla el contador de papelera).
     */
    protected function usesSoftDeletes(): bool
    {
        return true;
    }

    /**
     * Datos adicionales que se pasan como props a la vista `Index`.
     *
     * @return array<string, mixed>
     */
    protected function indexExtras(Request $request): array
    {
        return [];
    }

    /**
     * Permite personalizar la query del listado (p. ej. agregar `withCount`).
     * Devuelve `null` para usar `Model::query()` por defecto.
     */
    protected function indexQuery(): ?Builder
    {
        return null;
    }

    /**
     * Renderiza el listado principal de la entidad (vista `{viewPath}/Index`).
     *
     * Props que recibe la vista:
     * - `{routePrefix}`: colección de entidades (con relaciones eager-loaded).
     * - `trashedCount`: número de registros en papelera (si aplica soft deletes).
     * - `flash.success` / `flash.error`: mensajes de sesión.
     * - Extras devueltos por `indexExtras()`.
     *
     * @return Response Vista Inertia del listado.
     */
    public function index(Request $request): Response
    {
        $modelClass = $this->modelClass();

        $query = $this->indexQuery() ?? $modelClass::query();
        $entities = (clone $query)->with($this->relations())->latest()->get();
        $trashedCount = $this->usesSoftDeletes() ? $modelClass::onlyTrashed()->count() : 0;

        return Inertia::render($this->viewPath().'/Index', array_merge([
            $this->routePrefix() => $entities,
            'trashedCount' => $trashedCount,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ], $this->indexExtras($request)));
    }

    /**
     * Renderiza la papelera de la entidad (vista `{viewPath}/Trashed`).
     *
     * Props que recibe la vista:
     * - `{routePrefix}`: colección de entidades eliminadas (ordenadas por `deleted_at`).
     * - `flash.success` / `flash.error`: mensajes de sesión.
     *
     * @return Response Vista Inertia de la papelera.
     */
    public function trashed(): Response
    {
        $entities = $this->usesSoftDeletes()
            ? $this->modelClass()::with($this->relations())
                ->onlyTrashed()
                ->latest('deleted_at')
                ->get()
            : collect();

        return Inertia::render($this->viewPath().'/Trashed', [
            $this->routePrefix() => $entities,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }
}
