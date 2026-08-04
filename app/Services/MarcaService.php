<?php

namespace App\Services;

use App\Models\Marca;
use Illuminate\Database\Eloquent\Model;

/**
 * Servicio de dominio de Marcas.
 *
 * CRUD simple que impide eliminar una marca que tenga objetos asociados.
 */
readonly class MarcaService extends BaseCrudService
{
    public function __construct(
        private Marca $model,
    ) {
        parent::__construct($model);
    }

    /**
     * Una marca no puede eliminarse si tiene objetos asociados.
     */
    protected function hasDependents(Model $entity): bool
    {
        /** @var Marca $marca */
        $marca = $entity;

        return $marca->objetos()->count() > 0;
    }
}
