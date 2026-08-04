<?php

namespace App\Http\Requests\Categoria;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

/**
 * Valida la actualización de una categoría.
 *
 * El nombre es obligatorio y único ignorando la propia categoría y las
 * eliminadas (soft-deletes).
 */
class UpdateCategoriaRequest extends BaseFormRequest
{
    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
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
