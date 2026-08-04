<?php

namespace App\Services;

use App\Models\Objeto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

readonly class ObjetoService extends BaseCrudService
{
    public function __construct(
        private Objeto $model,
    ) {
        parent::__construct($model);
    }

    public function create(array $data): Objeto
    {
        unset($data['disponible']);
        $data['disponible'] = true;

        return DB::transaction(function () use ($data) {
            if (empty($data['codigo'])) {
                $data['codigo'] = Objeto::generarSiguienteCodigo();
            }

            $objeto = parent::create($data);

            if ($objeto->foto) {
                $newPath = app(ImageService::class)->renameImage($objeto->foto, $objeto->codigo);
                $objeto->update(['foto' => $newPath]);
            }

            return $objeto;
        });
    }

    public function update(Model $entity, array $data): Model
    {
        /** @var Objeto $objeto */
        $objeto = $entity;
        unset($data['disponible']);

        $fotoAnterior = $objeto->foto;
        $fotoNueva = $data['foto'] ?? $fotoAnterior;

        parent::update($objeto, $data);
        $objeto = $objeto->fresh();

        // Si la foto cambió (o se eliminó), borra el archivo anterior para
        // no dejar archivos huérfanos en disco.
        if ($fotoNueva !== $fotoAnterior && $fotoAnterior) {
            app(ImageService::class)->delete($fotoAnterior);
        }

        if ($objeto->foto) {
            $currentFilename = basename($objeto->foto);
            $expectedFilename = $objeto->codigo.'.jpg';

            if ($currentFilename !== $expectedFilename) {
                $newPath = app(ImageService::class)->renameImage($objeto->foto, $objeto->codigo);
                $objeto->update(['foto' => $newPath]);
            }
        }

        return $objeto->fresh();
    }

    protected function hasDependents(Model $entity): bool
    {
        /** @var Objeto $objeto */
        $objeto = $entity;

        return $objeto->movimientos()->count() > 0;
    }
}
