<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

/**
 * Servicio base para operaciones CRUD simples.
 *
 * Provee `create`, `update`, `delete` y `restore`. Las subclases sobrescriben
 * `hasDependents()` para impedir eliminar entidades referenciadas (p. ej. una
 * categoría con objetos) y los métodos de ciclo de vida para añadir efectos
 * secundarios (transacciones, notificaciones, recálculos).
 *
 * Está pensado como clase `readonly` y sus subclases deben declarar el modelo
 * concreto a través del constructor.
 */
readonly class BaseCrudService
{
    public function __construct(
        private Model $model,
    ) {}

    /**
     * Crea una entidad con los datos dados.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Actualiza una entidad y devuelve una instancia fresca.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Model $entity, array $data): Model
    {
        $entity->update($data);

        return $entity->fresh();
    }

    /**
     * Elimina (soft delete cuando aplica) la entidad.
     *
     * Devuelve `false` si la entidad tiene dependencias (`hasDependents`).
     */
    public function delete(Model $entity): bool
    {
        if ($this->hasDependents($entity)) {
            return false;
        }

        return $entity->delete();
    }

    /**
     * Restaura una entidad eliminada (requiere SoftDeletes).
     */
    public function restore(Model $entity): bool
    {
        return $entity->restore();
    }

    /**
     * Indica si la entidad tiene registros dependientes que impiden su borrado.
     */
    protected function hasDependents(Model $entity): bool
    {
        return false;
    }
}
