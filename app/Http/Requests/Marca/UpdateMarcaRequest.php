<?php

namespace App\Http\Requests\Marca;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

class UpdateMarcaRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $marca = $this->route('marca');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                new UniqueIgnoringSoftDeletes('marcas', 'nombre', $marca?->id),
            ],
        ];
    }
}
