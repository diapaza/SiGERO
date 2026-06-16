<?php

namespace App\Services;

use App\Models\Marca;

readonly class MarcaService
{
    public function __construct(
        private Marca $model,
    ) {}

    public function create(array $data): Marca
    {
        return $this->model->create($data);
    }

    public function update(Marca $marca, array $data): Marca
    {
        $marca->update($data);

        return $marca->fresh();
    }

    public function delete(Marca $marca): bool
    {
        if ($marca->objetos()->count() > 0) {
            return false;
        }

        return $marca->delete();
    }

    public function restore(Marca $marca): bool
    {
        return $marca->restore();
    }
}
