<?php

namespace App\Http\Requests\Movimiento;

use App\Enums\TipoMovimientoEnum;
use App\Http\Requests\BaseFormRequest;
use App\Rules\InEnum;

class StoreMovimientoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'objeto_id' => ['required', 'exists:objetos,id'],
            'registrado_por' => ['required', 'exists:users,id'],
            'tipo_movimiento' => ['required', new InEnum(TipoMovimientoEnum::class)],
            'fecha_hora' => ['required', 'date'],
        ];
    }
}
