<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Error pages
Route::get('/403', fn () => Inertia::render('Errors/FourZeroThree'))->name('errors.403');

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/signin', [AuthController::class, 'create'])->name('signin');
    Route::post('/signin', [AuthController::class, 'login'])->middleware('throttle:login')->name('login');
});

// Protected routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/read-all', [NotificationController::class, 'readAll'])->name('read-all');
        Route::post('/{notification}/read', [NotificationController::class, 'read'])->name('read');
    });

    // Dashboard
    Route::middleware('permission:ver dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });

    // Roles
    Route::middleware('permission:gestionar roles')->group(function () {
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });
    });

    // Usuarios
    Route::middleware('permission:ver usuarios|crear usuarios|editar usuarios|eliminar usuarios')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->middleware('permission:crear usuarios')->name('store');
            Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:editar usuarios')->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:eliminar usuarios')->name('destroy');
            Route::get('/trashed', [UserController::class, 'trashed'])->middleware('permission:editar usuarios')->name('trashed');
            Route::post('/{user}/restore', [UserController::class, 'restore'])->middleware('permission:editar usuarios')->withTrashed()->name('restore');
            Route::put('/{user}/permissions', [UserController::class, 'syncPermissions'])->middleware('permission:editar usuarios')->name('permissions.sync');
        });
    });

    // Categorías
    Route::middleware('permission:gestionar categorias')->group(function () {
        Route::prefix('categorias')->name('categorias.')->group(function () {
            Route::get('/', [CategoriaController::class, 'index'])->name('index');
            Route::post('/', [CategoriaController::class, 'store'])->name('store');
            Route::put('/{categoria}', [CategoriaController::class, 'update'])->name('update');
            Route::delete('/{categoria}', [CategoriaController::class, 'destroy'])->name('destroy');
            Route::get('/trashed', [CategoriaController::class, 'trashed'])->name('trashed');
            Route::post('/{categoria}/restore', [CategoriaController::class, 'restore'])->withTrashed()->name('restore');
        });
    });

    // Marcas
    Route::middleware('permission:gestionar marcas')->group(function () {
        Route::prefix('marcas')->name('marcas.')->group(function () {
            Route::get('/', [MarcaController::class, 'index'])->name('index');
            Route::post('/', [MarcaController::class, 'store'])->name('store');
            Route::put('/{marca}', [MarcaController::class, 'update'])->name('update');
            Route::delete('/{marca}', [MarcaController::class, 'destroy'])->name('destroy');
            Route::get('/trashed', [MarcaController::class, 'trashed'])->name('trashed');
            Route::post('/{marca}/restore', [MarcaController::class, 'restore'])->withTrashed()->name('restore');
        });
    });

    // Objetos
    Route::middleware('permission:gestionar objetos')->group(function () {
        Route::prefix('objetos')->name('objetos.')->group(function () {
            Route::get('/', [ObjetoController::class, 'index'])->name('index');
            Route::post('/', [ObjetoController::class, 'store'])->name('store');
            Route::put('/{objeto}', [ObjetoController::class, 'update'])->name('update');
            Route::delete('/{objeto}', [ObjetoController::class, 'destroy'])->name('destroy');
            Route::get('/trashed', [ObjetoController::class, 'trashed'])->name('trashed');
            Route::post('/{objeto}/restore', [ObjetoController::class, 'restore'])->withTrashed()->name('restore');
            Route::post('/upload-image', [ObjetoController::class, 'uploadImage'])->name('upload-image');
            Route::post('/delete-image', [ObjetoController::class, 'deleteImage'])->name('delete-image');
        });
    });

    // Movimientos
    Route::middleware('permission:registrar movimientos')->group(function () {
        Route::prefix('movimientos')->name('movimientos.')->group(function () {
            Route::get('/', [MovimientoController::class, 'index'])->name('index');
            Route::post('/', [MovimientoController::class, 'store'])->name('store');
            Route::put('/{movimiento}', [MovimientoController::class, 'update'])->name('update');
            Route::delete('/{movimiento}', [MovimientoController::class, 'destroy'])->name('destroy');
        });
    });

    // API - Búsquedas
    Route::middleware('permission:gestionar objetos|registrar movimientos')->group(function () {
        Route::get('/api/objetos/search/{codigo}', [ApiController::class, 'searchObjeto'])->name('api.objetos.search');
    });

    Route::middleware('permission:ver usuarios')->group(function () {
        Route::get('/api/users/search/{dni}', [ApiController::class, 'searchUser'])->name('api.users.search');
    });
});
