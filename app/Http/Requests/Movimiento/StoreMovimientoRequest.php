<?php

namespace App\Http\Requests\Movimiento;

use App\Enums\TipoMovimientoEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMovimientoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'objeto_id' => ['required', 'exists:objetos,id'],
            'registrado_por' => ['required', 'exists:users,id'],
            'tipo_movimiento' => ['required', new Enum(TipoMovimientoEnum::class)],
            'fecha_hora' => ['required', 'date'],
        ];
    }
}
