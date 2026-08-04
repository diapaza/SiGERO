<?php

namespace App\Http\Requests\Movimiento;

use App\Enums\TipoMovimientoEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * Valida la edición de un movimiento.
 *
 * `objeto_id` NO se acepta (el objeto de un movimiento es inmutable para
 * evitar disponibilidades inconsistentes); se permite cambiar el responsable,
 * el tipo y la fecha.
 */
class UpdateMovimientoRequest extends BaseFormRequest
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
            'tipo_movimiento' => ['required', new Enum(TipoMovimientoEnum::class)],
            'fecha_hora' => ['required', 'date'],
        ];
    }
}
