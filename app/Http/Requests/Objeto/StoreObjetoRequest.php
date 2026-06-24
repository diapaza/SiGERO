<?php

namespace App\Http\Requests\Objeto;

use App\Http\Requests\BaseFormRequest;

class StoreObjetoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'codigo' => ['nullable', 'string', 'max:12'],
            'nombre' => ['required', 'string', 'max:150'],
            'modelo' => ['nullable', 'string', 'max:250'],
            'descripcion' => ['nullable', 'string'],
            'marca_id' => ['nullable', 'exists:marcas,id'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'foto' => ['nullable', 'string', 'max:2048'],
            'serie' => ['nullable', 'string', 'max:50'],
            'disponible' => ['boolean'],
        ];
    }
}
