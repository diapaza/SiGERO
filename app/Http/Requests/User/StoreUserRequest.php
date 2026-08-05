<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

/**
 * Valida la creación de un usuario.
 *
 * `username` y `dni` son únicos; `roles` es una lista de nombres de rol
 * existentes; la contraseña es obligatoria, de mínimo 8 caracteres y debe
 * confirmarse.
 */
class StoreUserRequest extends BaseFormRequest
{
    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'dni' => ['required', 'string', 'size:8', 'unique:users,dni'],
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'whatsapp_number' => ['required', 'string', 'size:9', 'regex:/^\d{9}$/'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
