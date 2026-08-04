<?php

namespace App\Http\Requests\Objeto;

use App\Http\Requests\BaseFormRequest;

/**
 * Valida la edición de un objeto.
 *
 * El `codigo` es opcional en la petición, pero el controlador lo descarta
 * (`unset`): el código de un objeto es inmutable al editar.
 */
class UpdateObjetoRequest extends BaseFormRequest
{
    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $objeto = $this->route('objeto');

        return [
            'codigo' => ['nullable', 'string', 'max:12'],
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
