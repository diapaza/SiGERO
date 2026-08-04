<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest base de la aplicación.
 *
 * Todos los FormRequests heredan de esta clase, lo que garantiza:
 * - Autorización por defecto (cada request concreto define sus reglas).
 * - Sanitización previa a la validación: se aplica `trim()` a todos los
 *   campos de tipo string antes de validar.
 */
abstract class BaseFormRequest extends FormRequest
{
    /**
     * Reglas de validación de la petición.
     *
     * @return array<string, mixed>
     */
    abstract public function rules(): array;

    /**
     * Autoriza la petición. Por defecto se permite el paso; el control de
     * acceso se resuelve con middleware de permisos en las rutas.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Preparación previa a la validación: recorta espacios en los campos string.
     */
    public function prepareForValidation(): void
    {
        $this->sanitizeStringFields();
    }

    /**
     * Aplica `trim()` a todos los valores de tipo string de la petición.
     */
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
