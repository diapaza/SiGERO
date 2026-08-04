<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Regla de validación "único ignorando soft-deletes".
 *
 * Comprueba que un valor no exista en la columna indicada, excluyendo los
 * registros con `deleted_at` no nulo y (opcionalmente) un ID concreto. Se usa
 * para que se pueda volver a usar nombres/códigos de entidades en papelera.
 */
class UniqueIgnoringSoftDeletes implements ValidationRule
{
    public function __construct(
        private readonly string $table,
        private readonly string $column,
        private readonly int|string|null $ignoreId = null,
    ) {}

    /**
     * Crea la regla de forma fluida indicando tabla, columna e ID a ignorar.
     */
    public static function for(string $table, string $column, int|string|null $ignoreId = null): static
    {
        return new static($table, $column, $ignoreId);
    }

    /**
     * Crea la regla a partir de un modelo Eloquent (usa su tabla y columna).
     */
    public static function forModel(Model $model, string $column, int|string|null $ignoreId = null): static
    {
        return new static($model->getTable(), $column, $ignoreId);
    }

    /**
     * Ejecuta la validación; falla con `validation.unique` si el valor existe.
     *
     * @param  string  $attribute  Nombre del atributo validado.
     * @param  mixed  $value  Valor a comprobar.
     * @param  Closure  $fail  Callback que registra el error.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table($this->table)
            ->where($this->column, $value)
            ->whereNull('deleted_at');

        if ($this->ignoreId !== null) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('validation.unique')->translate();
        }
    }
}
