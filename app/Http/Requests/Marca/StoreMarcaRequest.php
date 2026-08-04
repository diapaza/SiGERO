<?php

namespace App\Http\Requests\Marca;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

/**
 * Valida la creación de una marca.
 *
 * El nombre es obligatorio y único **ignorando** las marcas en papelera
 * (soft-deleted).
 */
class StoreMarcaRequest extends BaseFormRequest
{
    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
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
