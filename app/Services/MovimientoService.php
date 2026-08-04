<?php

namespace App\Services;

use App\Models\Movimiento;
use App\Models\Objeto;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Servicio de dominio de los movimientos de préstamo (salidas y retornos).
 *
 * Es el único responsable de mantener la consistencia del préstamo:
 * - Valida los invariantes (no doble salida; retorno solo si está prestado).
 * - Deriva el flag `disponible` del objeto a partir de su historial.
 * - Dispara las notificaciones de salida/retorno a los operadores.
 * - Protege las operaciones con transacciones y locks para evitar carreras.
 */
readonly class MovimientoService extends BaseCrudService
{
    public function __construct(
        private Movimiento $model,
        private NotificationService $notifications,
    ) {
        parent::__construct($model);
    }

    /**
     * Crea un movimiento validando invariantes y recalculando la disponibilidad.
     *
     * La operación se ejecuta dentro de una transacción que bloquea la fila
     * del objeto (`lockForUpdate`) para evitar salidas simultáneas. Tras el
     * alta, notifica a los operadores según el tipo de movimiento.
     *
     * @param  array<string, mixed>  $data  Datos validados (user_id, objeto_id, tipo_movimiento, fecha_hora).
     */
    public function create(array $data): Movimiento
    {
        $movimiento = DB::transaction(function () use ($data) {
            // Serializa el acceso al objeto para evitar salidas simultáneas.
            Objeto::whereKey($data['objeto_id'])->lockForUpdate()->first();

            $this->assertTipoValido(
                $data['objeto_id'],
                $data['tipo_movimiento'],
            );

            $movimiento = parent::create($data);

            $this->recalcularDisponibilidad($movimiento->objeto_id);

            return $movimiento;
        });

        $this->notifyMovimiento($movimiento);

        return $movimiento;
    }

    /**
     * Actualiza un movimiento (responsable, tipo y fecha).
     *
     * El `objeto_id` se descarta (inmutable). Si cambia el tipo de movimiento,
     * se recalcula la disponibilidad del objeto.
     *
     * @return Model|Movimiento Movimiento actualizado (fresh).
     */
    public function update(Model $entity, array $data): Model
    {
        /** @var Movimiento $movimiento */
        $movimiento = $entity;

        // El objeto de un movimiento no puede cambiarse: la disponibilidad
        // de ambos objetos quedaría inconsistente.
        unset($data['objeto_id']);

        $tipoAnterior = $movimiento->tipo_movimiento?->value;
        $tipoNuevo = $data['tipo_movimiento'] ?? $tipoAnterior;

        $this->assertTipoValido(
            $movimiento->objeto_id,
            $tipoNuevo,
            excludeMovimientoId: $movimiento->id,
        );

        parent::update($movimiento, $data);
        $movimiento = $movimiento->fresh();

        if ($tipoAnterior !== $tipoNuevo) {
            $this->recalcularDisponibilidad($movimiento->objeto_id);
        }

        return $movimiento;
    }

    /**
     * Elimina (soft delete) un movimiento y recalcula la disponibilidad.
     */
    public function delete(Model $entity): bool
    {
        /** @var Movimiento $movimiento */
        $movimiento = $entity;
        $result = parent::delete($movimiento);

        if ($result) {
            $this->recalcularDisponibilidad($movimiento->objeto_id);
        }

        return $result;
    }

    /**
     * Restaura un movimiento eliminado y recalcula la disponibilidad.
     */
    public function restore(Model $entity): bool
    {
        /** @var Movimiento $movimiento */
        $movimiento = $entity;
        $result = parent::restore($movimiento);

        if ($result) {
            $this->recalcularDisponibilidad($movimiento->objeto_id);
        }

        return $result;
    }

    /**
     * Valida los invariantes del préstamo: un objeto solo puede tener una salida
     * activa a la vez, y un retorno solo es válido si el objeto está prestado.
     *
     * En caso de violación responde HTTP 422 con un mensaje descriptivo.
     *
     * @param  int  $objetoId  ID del objeto involucrado.
     * @param  string  $tipo  Tipo del movimiento (`salida` | `retorno`).
     * @param  int|null  $excludeMovimientoId  Movimiento a ignorar al buscar el último (edición).
     */
    private function assertTipoValido(
        int $objetoId,
        string $tipo,
        ?int $excludeMovimientoId = null,
    ): void {
        $objeto = Objeto::find($objetoId);

        if (! $objeto) {
            abort(422, 'El objeto no existe.');
        }

        $ultimoMovimiento = Movimiento::where('objeto_id', $objetoId)
            ->when($excludeMovimientoId, fn ($query) => $query->where('id', '!=', $excludeMovimientoId))
            ->latest('fecha_hora')
            ->first();

        $estaPrestado = $ultimoMovimiento?->tipo_movimiento?->value === 'salida';

        if ($tipo === 'salida' && $estaPrestado) {
            abort(422, 'El objeto ya está prestado.');
        }

        if ($tipo === 'retorno' && ! $estaPrestado) {
            abort(422, 'El objeto no está prestado.');
        }
    }

    /**
     * Deriva el flag `disponible` del historial real de movimientos.
     *
     * Un objeto está disponible salvo que su último movimiento sea una salida.
     *
     * @param  int  $objetoId  ID del objeto a recalcular.
     */
    private function recalcularDisponibilidad(int $objetoId): void
    {
        $ultimoMovimiento = Movimiento::where('objeto_id', $objetoId)
            ->latest('fecha_hora')
            ->first();

        $disponible = $ultimoMovimiento?->tipo_movimiento?->value !== 'salida';

        Objeto::where('id', $objetoId)->update(['disponible' => $disponible]);
    }

    /**
     * Notifica a los operadores la salida o retorno registrado.
     */
    private function notifyMovimiento(Movimiento $movimiento): void
    {
        $registradoPor = User::find($movimiento->registrado_por);

        if (! $registradoPor) {
            return;
        }

        $movimiento->load('objeto');

        if ($movimiento->tipo_movimiento->value === 'salida') {
            $this->notifications->salidaRegistrada($movimiento, $registradoPor);

            return;
        }

        $this->notifications->retornoRegistrado($movimiento, $registradoPor);
    }
}
