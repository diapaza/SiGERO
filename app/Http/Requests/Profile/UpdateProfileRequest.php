<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\BaseFormRequest;

/**
 * Valida la actualización del perfil del usuario autenticado.
 *
 * Solo los administradores pueden modificar el `username`. El DNI es único
 * ignorando el propio usuario.
 */
class UpdateProfileRequest extends BaseFormRequest
{
    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->user();
        $isAdmin = $user->hasRole('Administrador');

        $rules = [
            'dni' => [
                'required',
                'string',
                'size:8',
                'unique:users,dni,'.$user->id,
            ],
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'whatsapp_number' => ['nullable', 'string', 'max:15'],
        ];

        if ($isAdmin) {
            $rules['username'] = [
                'required',
                'string',
                'max:255',
                'unique:users,username,'.$user->id,
            ];
        }

        return $rules;
    }
}
