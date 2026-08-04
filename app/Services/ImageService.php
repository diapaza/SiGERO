<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Servicio de procesamiento de imágenes de objetos.
 *
 * Usa Intervention Image con el driver GD: redimensiona a un tamaño máximo,
 * convierte a JPEG con calidad configurable y guarda en
 * `storage/app/public/{directory}/`. También renombra y elimina imágenes con
 * protección anti path-traversal (solo opera dentro de directorios permitidos).
 */
readonly class ImageService
{
    /**
     * Directorios permitidos para operaciones de borrado.
     *
     * @var list<string>
     */
    private const ALLOWED_DIRECTORIES = ['objetos'];

    /**
     * Gestor de imágenes de Intervention.
     */
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    /**
     * Procesa y guarda una imagen subida.
     *
     * Redimensiona a `maxSize` px (máximo, manteniendo proporción), la
     * convierte a JPEG con la calidad dada y la guarda en el directorio.
     * Devuelve la ruta relativa almacenada (p. ej. `objetos/1712...jpg`).
     *
     * @param  string  $directory  Subdirectorio bajo `storage/app/public`.
     * @param  string|null  $filename  Nombre base del archivo; si es `null` se genera uno único.
     * @param  int  $maxSize  Tamaño máximo en píxeles del lado mayor.
     * @param  int  $quality  Calidad JPEG (0-100).
     */
    public function process(UploadedFile $file, string $directory = 'objetos', ?string $filename = null, int $maxSize = 800, int $quality = 80): string
    {
        $this->ensureDirectory($directory);

        $image = $this->manager->read($file);

        $image->scaleDown(width: $maxSize, height: $maxSize);

        if (! $filename) {
            $filename = time().'_'.uniqid();
        }

        $path = storage_path("app/public/{$directory}/{$filename}.jpg");

        $image->toJpeg($quality)->save($path);

        return "{$directory}/{$filename}.jpg";
    }

    /**
     * Renombra una imagen a `{newCode}.jpg` dentro del directorio.
     *
     * Si el archivo de origen no existe, devuelve la ruta original sin cambios.
     *
     * @param  string  $oldPath  Ruta relativa de la imagen actual.
     * @param  string  $newCode  Nuevo nombre base (típicamente el código del objeto).
     */
    public function renameImage(string $oldPath, string $newCode, string $directory = 'objetos'): string
    {
        $fullOldPath = storage_path("app/public/{$oldPath}");

        if (! file_exists($fullOldPath)) {
            return $oldPath;
        }

        $this->ensureDirectory($directory);

        $newPath = storage_path("app/public/{$directory}/{$newCode}.jpg");

        rename($fullOldPath, $newPath);

        return "{$directory}/{$newCode}.jpg";
    }

    /**
     * Crea el directorio de destino si no existe (p. ej. tras un despliegue
     * limpio, donde la carpeta puede no estar en el repositorio).
     */
    private function ensureDirectory(string $directory): void
    {
        $path = storage_path("app/public/{$directory}");

        File::ensureDirectoryExists($path);
    }

    /**
     * Elimina una imagen si existe, validando que esté dentro de los
     * directorios permitidos.
     *
     * @param  string  $path  Ruta relativa de la imagen.
     */
    public function delete(string $path): bool
    {
        $fullPath = $this->resolvePublicPath($path);

        if (! $fullPath) {
            return false;
        }

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    /**
     * Resuelve una ruta relativa a un archivo real dentro de los directorios
     * permitidos, previniendo path traversal.
     */
    private function resolvePublicPath(string $path): ?string
    {
        $base = realpath(storage_path('app/public'));

        if (! $base) {
            return null;
        }

        $segments = explode('/', str_replace('\\', '/', $path));

        if (count($segments) < 2 || ! in_array($segments[0], self::ALLOWED_DIRECTORIES, true)) {
            return null;
        }

        $fullPath = realpath(storage_path("app/public/{$path}"));

        if ($fullPath === false) {
            return null;
        }

        if (! Str::startsWith($fullPath, $base.DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $fullPath;
    }
}
