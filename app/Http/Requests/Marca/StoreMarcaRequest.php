<?php

namespace App\Http\Requests\Marca;

use App\Http\Requests\BaseFormRequest;

class StoreMarcaRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:marcas,nombre'],
        ];
    }
}
