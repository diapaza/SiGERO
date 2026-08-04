<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseFormRequest;

/**
 * Valida la creación de un rol.
 *
 * El `name` es obligatorio y único; `guard_name` por defecto será `web`
 * (lo aplica `RoleService`).
 */
class StoreRoleRequest extends BaseFormRequest
{
    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
