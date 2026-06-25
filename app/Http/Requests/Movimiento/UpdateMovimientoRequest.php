<?php

namespace App\Http\Requests\Movimiento;

use App\Enums\TipoMovimientoEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateMovimientoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'objeto_id' => ['sometimes', 'exists:objetos,id'],
            'tipo_movimiento' => ['required', new Enum(TipoMovimientoEnum::class)],
            'fecha_hora' => ['required', 'date'],
        ];
    }
}
