<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseFormRequest;

/**
 * Valida la actualización de un rol.
 *
 * El `name` es obligatorio y único ignorando el propio rol.
 */
class UpdateRoleRequest extends BaseFormRequest
{
    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name,'.$role?->id,
            ],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
