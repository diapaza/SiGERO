<?php

namespace App\Http\Requests\Marca;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

class StoreMarcaRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                new UniqueIgnoringSoftDeletes('marcas', 'nombre'),
            ],
        ];
    }
}
