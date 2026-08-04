<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class UpdateUserRequest extends BaseFormRequest
{
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
            'whatsapp_number' => ['nullable', 'string', 'max:15'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
