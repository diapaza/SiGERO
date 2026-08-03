<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

abstract class BaseCrudController extends Controller
{
    abstract protected function modelClass(): string;

    abstract protected function viewPath(): string;

    abstract protected function routePrefix(): string;

    abstract protected function label(): string;

    protected function relations(): array
    {
        return [];
    }

    protected function usesSoftDeletes(): bool
    {
        return true;
    }

    protected function indexExtras(Request $request): array
    {
        return [];
    }

    protected function indexQuery(): ?Builder
    {
        return null;
    }

    public function index(Request $request): Response
    {
        $modelClass = $this->modelClass();

        $query = $this->indexQuery() ?? $modelClass::query();
        $entities = (clone $query)->with($this->relations())->latest()->get();
        $trashedCount = $this->usesSoftDeletes() ? $modelClass::onlyTrashed()->count() : 0;

        return Inertia::render($this->viewPath() . '/Index', array_merge([
            $this->routePrefix() => $entities,
            'trashedCount' => $trashedCount,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ], $this->indexExtras($request)));
    }

    public function trashed(): Response
    {
        $entities = $this->usesSoftDeletes()
            ? $this->modelClass()::with($this->relations())
                ->onlyTrashed()
                ->latest('deleted_at')
                ->get()
            : collect();

        return Inertia::render($this->viewPath() . '/Trashed', [
            $this->routePrefix() => $entities,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }
}
