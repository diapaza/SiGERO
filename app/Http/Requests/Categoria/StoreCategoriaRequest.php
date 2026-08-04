<?php

namespace App\Http\Requests\Categoria;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

class StoreCategoriaRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                new UniqueIgnoringSoftDeletes('categorias', 'nombre'),
            ],
        ];
    }
}
