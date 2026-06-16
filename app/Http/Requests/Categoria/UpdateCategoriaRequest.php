<?php

namespace App\Http\Requests\Categoria;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

class UpdateCategoriaRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $categoria = $this->route('categoria');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                new UniqueIgnoringSoftDeletes('categorias', 'nombre', $categoria?->id),
            ],
        ];
    }
}
