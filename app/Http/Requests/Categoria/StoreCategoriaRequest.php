<?php

namespace App\Http\Requests\Categoria;

use App\Http\Requests\BaseFormRequest;

class StoreCategoriaRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:categorias,nombre'],
        ];
    }
}
