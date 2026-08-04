<?php

namespace App\Http\Requests\Movimiento;

use App\Enums\TipoMovimientoEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * Valida el registro de un movimiento de salida o retorno.
 *
 * El `registrado_por` NO se valida aquí: lo asigna el controlador con el
 * usuario autenticado para evitar que el cliente forje la autoría.
 */
class StoreMovimientoRequest extends BaseFormRequest
{
    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'objeto_id' => ['required', 'exists:objetos,id'],
            'tipo_movimiento' => ['required', new Enum(TipoMovimientoEnum::class)],
            'fecha_hora' => ['required', 'date'],
        ];
    }
}
