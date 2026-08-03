<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

readonly class ImageService
{
    private const ALLOWED_DIRECTORIES = ['objetos'];

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function process(UploadedFile $file, string $directory = 'objetos', ?string $filename = null, int $maxSize = 800, int $quality = 80): string
    {
        $image = $this->manager->read($file);

        $image->scaleDown(width: $maxSize, height: $maxSize);

        if (! $filename) {
            $filename = time() . '_' . uniqid();
        }

        $path = storage_path("app/public/{$directory}/{$filename}.jpg");

        $image->toJpeg($quality)->save($path);

        return "{$directory}/{$filename}.jpg";
    }

    public function renameImage(string $oldPath, string $newCode, string $directory = 'objetos'): string
    {
        $fullOldPath = storage_path("app/public/{$oldPath}");

        if (! file_exists($fullOldPath)) {
            return $oldPath;
        }

        $newPath = storage_path("app/public/{$directory}/{$newCode}.jpg");

        rename($fullOldPath, $newPath);

        return "{$directory}/{$newCode}.jpg";
    }

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

        if (! Str::startsWith($fullPath, $base . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $fullPath;
    }
}
