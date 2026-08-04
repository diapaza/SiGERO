<?php

namespace App\Services;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Model;

/**
 * Servicio de dominio de Categorías.
 *
 * CRUD simple que impide eliminar una categoría que tenga objetos asociados.
 */
readonly class CategoriaService extends BaseCrudService
{
    public function __construct(
        private Categoria $model,
    ) {
        parent::__construct($model);
    }

    /**
     * Una categoría no puede eliminarse si tiene objetos asociados.
     */
    protected function hasDependents(Model $entity): bool
    {
        /** @var Categoria $categoria */
        $categoria = $entity;

        return $categoria->objetos()->count() > 0;
    }
}
