<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

/**
 * Valida la edición de un usuario.
 *
 * `username` y `dni` son únicos ignorando al propio usuario; la contraseña es
 * opcional (si llega, debe tener mínimo 8 caracteres y confirmarse).
 */
class UpdateUserRequest extends BaseFormRequest
{
    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username,'.$user?->id,
            ],
            'dni' => [
                'required',
                'string',
                'size:8',
                'unique:users,dni,'.$user?->id,
            ],
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'whatsapp_number' => ['required', 'string', 'size:9', 'regex:/^\d{9}$/'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
