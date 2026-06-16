<?php

namespace App\Services;

use App\Models\Categoria;

readonly class CategoriaService
{
    public function __construct(
        private Categoria $model,
    ) {}

    public function create(array $data): Categoria
    {
        return $this->model->create($data);
    }

    public function update(Categoria $categoria, array $data): Categoria
    {
        $categoria->update($data);

        return $categoria->fresh();
    }

    public function delete(Categoria $categoria): bool
    {
        if ($categoria->objetos()->count() > 0) {
            return false;
        }

        return $categoria->delete();
    }

    public function restore(Categoria $categoria): bool
    {
        return $categoria->restore();
    }
}
