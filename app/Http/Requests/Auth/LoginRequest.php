<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

/**
 * Valida la petición de inicio de sesión.
 *
 * Requiere `username` y `password` como strings no vacíos.
 */
class LoginRequest extends BaseFormRequest
{
    /**
     * Reglas de validación del login.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }
}
