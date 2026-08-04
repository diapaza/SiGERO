<?php

namespace App\Http\Requests\Categoria;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

/**
 * Valida la creación de una categoría.
 *
 * El nombre es obligatorio y único **ignorando** las categorías en papelera
 * (soft-deleted), de modo que se pueda volver a crear un nombre ya eliminado.
 */
class StoreCategoriaRequest extends BaseFormRequest
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
                new UniqueIgnoringSoftDeletes('categorias', 'nombre'),
            ],
        ];
    }
}
