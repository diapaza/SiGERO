<?php

namespace App\Services;

use App\Models\Movimiento;

readonly class MovimientoService
{
    public function __construct(
        private Movimiento $model,
    ) {}

    public function create(array $data): Movimiento
    {
        return $this->model->create($data);
    }

    public function update(Movimiento $movimiento, array $data): Movimiento
    {
        $movimiento->update($data);

        return $movimiento->fresh();
    }

    public function delete(Movimiento $movimiento): bool
    {
        return $movimiento->delete();
    }

    public function restore(Movimiento $movimiento): bool
    {
        return $movimiento->restore();
    }
}
