<?php

namespace App\Providers;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Movimiento;
use App\Models\Objeto;
use App\Models\Rol;
use App\Models\User;
use App\Services\CategoriaService;
use App\Services\MarcaService;
use App\Services\MovimientoService;
use App\Services\ObjetoService;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RoleService::class, fn () => new RoleService(new Rol));
        $this->app->bind(CategoriaService::class, fn () => new CategoriaService(new Categoria));
        $this->app->bind(MarcaService::class, fn () => new MarcaService(new Marca));
        $this->app->bind(ObjetoService::class, fn () => new ObjetoService(new Objeto));
        $this->app->bind(MovimientoService::class, fn () => new MovimientoService(new Movimiento));
        $this->app->bind(UserService::class, fn () => new UserService(new User));
    }

    public function boot(): void
    {
        //
    }
}
