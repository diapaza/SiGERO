<?php

namespace App\Http\Requests\Marca;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

/**
 * Valida la actualización de una marca.
 *
 * El nombre es obligatorio y único ignorando la propia marca y las eliminadas
 * (soft-deletes).
 */
class UpdateMarcaRequest extends BaseFormRequest
{
    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
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
