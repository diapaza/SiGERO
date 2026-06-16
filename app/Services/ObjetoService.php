<?php

namespace App\Services;

use App\Models\Objeto;

readonly class ObjetoService
{
    public function __construct(
        private Objeto $model,
    ) {}

    public function create(array $data): Objeto
    {
        return $this->model->create($data);
    }

    public function update(Objeto $objeto, array $data): Objeto
    {
        $objeto->update($data);

        return $objeto->fresh();
    }

    public function delete(Objeto $objeto): bool
    {
        if ($objeto->movimientos()->count() > 0) {
            return false;
        }

        return $objeto->delete();
    }

    public function restore(Objeto $objeto): bool
    {
        return $objeto->restore();
    }
}
