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
        $movimiento = $this->model->create($data);

        $this->updateObjetoDisponibilidad(
            $movimiento->objeto_id,
            $data['tipo_movimiento'] === 'salida' ? false : true,
        );

        return $movimiento;
    }

    public function update(Movimiento $movimiento, array $data): Movimiento
    {
        $tipoAnterior = $movimiento->tipo_movimiento;
        $movimiento->update($data);

        $tipoNuevo = $data['tipo_movimiento'];

        if ($tipoAnterior !== $tipoNuevo) {
            $this->updateObjetoDisponibilidad(
                $movimiento->objeto_id,
                $tipoNuevo === 'salida' ? false : true,
            );
        }

        return $movimiento->fresh();
    }

    public function delete(Movimiento $movimiento): bool
    {
        $result = $movimiento->delete();

        if ($result) {
            $this->updateObjetoDisponibilidad(
                $movimiento->objeto_id,
                $movimiento->tipo_movimiento === 'salida' ? true : false,
            );
        }

        return $result;
    }

    public function restore(Movimiento $movimiento): bool
    {
        $result = $movimiento->restore();

        if ($result) {
            $this->updateObjetoDisponibilidad(
                $movimiento->objeto_id,
                $movimiento->tipo_movimiento === 'salida' ? false : true,
            );
        }

        return $result;
    }

    private function updateObjetoDisponibilidad(int $objetoId, bool $disponible): void
    {
        \App\Models\Objeto::where('id', $objetoId)->update(['disponible' => $disponible]);
    }
}
