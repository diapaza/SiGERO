<?php

namespace App\Http\Requests\Objeto;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

/**
 * Valida la creación de un objeto.
 *
 * El `codigo` es opcional (se auto-genera si está vacío), debe ser de 4 o 12
 * dígitos y único ignorando los objetos eliminados (soft-deletes).
 */
class StoreObjetoRequest extends BaseFormRequest
{
    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'codigo' => [
                'nullable',
                'string',
                'max:12',
                'regex:/^(\d{4}|\d{12})$/',
                new UniqueIgnoringSoftDeletes('objetos', 'codigo'),
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'modelo' => ['nullable', 'string', 'max:250'],
            'descripcion' => ['nullable', 'string'],
            'marca_id' => ['nullable', 'exists:marcas,id'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'foto' => ['nullable', 'string', 'max:2048'],
            'serie' => ['nullable', 'string', 'max:50'],
        ];
    }
}
