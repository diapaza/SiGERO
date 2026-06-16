<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    abstract public function rules(): array;

    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->sanitizeStringFields();
    }

    protected function sanitizeStringFields(): void
    {
        $attributes = $this->all();

        foreach ($attributes as $key => $value) {
            if (is_string($value)) {
                $attributes[$key] = trim($value);
            }
        }

        $this->merge($attributes);
    }
}
