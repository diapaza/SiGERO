<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/signin', [AuthController::class, 'create'])->name('signin');
    Route::post('/signin', [AuthController::class, 'login'])->name('login');
    Route::get('/signup', fn () => Inertia::render('Auth/Signup'))->name('signup');
});

// Protected routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', fn () => Inertia::render('Dashboard'))->name('dashboard');

    // Features
    Route::get('/calendar', fn () => Inertia::render('Others/Calendar'))->name('calendar');
    Route::get('/profile', fn () => Inertia::render('Others/UserProfile'))->name('profile');
    Route::get('/form-elements', fn () => Inertia::render('Forms/FormElements'))->name('form-elements');
    Route::get('/basic-tables', fn () => Inertia::render('Tables/BasicTables'))->name('basic-tables');
    Route::get('/data-tables', fn () => Inertia::render('Tables/DataTables'))->name('data-tables');

    // Charts
    Route::get('/line-chart', fn () => Inertia::render('Chart/LineChart/LineChart'))->name('line-chart');
    Route::get('/bar-chart', fn () => Inertia::render('Chart/BarChart/BarChart'))->name('bar-chart');

    // UI Elements
    Route::get('/alerts', fn () => Inertia::render('UiElements/Alerts'))->name('alerts');
    Route::get('/avatars', fn () => Inertia::render('UiElements/Avatars'))->name('avatars');
    Route::get('/badge', fn () => Inertia::render('UiElements/Badges'))->name('badge');
    Route::get('/buttons', fn () => Inertia::render('UiElements/Buttons'))->name('buttons');
    Route::get('/modal', fn () => Inertia::render('UiElements/Modal'))->name('modal');
    Route::get('/dialogs', fn () => Inertia::render('UiElements/Dialogs'))->name('dialogs');
    Route::get('/toasts', fn () => Inertia::render('UiElements/Toasts'))->name('toasts');
    Route::get('/images', fn () => Inertia::render('UiElements/Images'))->name('images');
    Route::get('/videos', fn () => Inertia::render('UiElements/Videos'))->name('videos');

    // Pages
    Route::get('/blank', fn () => Inertia::render('Pages/BlankPage'))->name('blank');
    Route::get('/error-404', fn () => Inertia::render('Errors/FourZeroFour'))->name('404');

    // Roles
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        Route::get('/trashed', [RoleController::class, 'trashed'])->name('trashed');
        Route::post('/{role}/restore', [RoleController::class, 'restore'])->name('restore')->withTrashed();
    });

    // Categorías
    Route::prefix('categorias')->name('categorias.')->group(function () {
        Route::get('/', [CategoriaController::class, 'index'])->name('index');
        Route::post('/', [CategoriaController::class, 'store'])->name('store');
        Route::put('/{categoria}', [CategoriaController::class, 'update'])->name('update');
        Route::delete('/{categoria}', [CategoriaController::class, 'destroy'])->name('destroy');
        Route::get('/trashed', [CategoriaController::class, 'trashed'])->name('trashed');
        Route::post('/{categoria}/restore', [CategoriaController::class, 'restore'])->name('restore')->withTrashed();
    });

    // Marcas
    Route::prefix('marcas')->name('marcas.')->group(function () {
        Route::get('/', [MarcaController::class, 'index'])->name('index');
        Route::post('/', [MarcaController::class, 'store'])->name('store');
        Route::put('/{marca}', [MarcaController::class, 'update'])->name('update');
        Route::delete('/{marca}', [MarcaController::class, 'destroy'])->name('destroy');
        Route::get('/trashed', [MarcaController::class, 'trashed'])->name('trashed');
        Route::post('/{marca}/restore', [MarcaController::class, 'restore'])->name('restore')->withTrashed();
    });

    // Objetos
    Route::prefix('objetos')->name('objetos.')->group(function () {
        Route::get('/', [ObjetoController::class, 'index'])->name('index');
        Route::post('/', [ObjetoController::class, 'store'])->name('store');
        Route::put('/{objeto}', [ObjetoController::class, 'update'])->name('update');
        Route::delete('/{objeto}', [ObjetoController::class, 'destroy'])->name('destroy');
        Route::get('/trashed', [ObjetoController::class, 'trashed'])->name('trashed');
        Route::post('/{objeto}/restore', [ObjetoController::class, 'restore'])->name('restore')->withTrashed();
        Route::post('/upload-image', [ObjetoController::class, 'uploadImage'])->name('upload-image');
        Route::post('/delete-image', [ObjetoController::class, 'deleteImage'])->name('delete-image');
    });

    // Movimientos
    Route::prefix('movimientos')->name('movimientos.')->group(function () {
        Route::get('/', [MovimientoController::class, 'index'])->name('index');
        Route::post('/', [MovimientoController::class, 'store'])->name('store');
        Route::put('/{movimiento}', [MovimientoController::class, 'update'])->name('update');
        Route::delete('/{movimiento}', [MovimientoController::class, 'destroy'])->name('destroy');
        Route::get('/trashed', [MovimientoController::class, 'trashed'])->name('trashed');
        Route::post('/{movimiento}/restore', [MovimientoController::class, 'restore'])->name('restore')->withTrashed();
    });

    // Usuarios
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/trashed', [UserController::class, 'trashed'])->name('trashed');
        Route::post('/{user}/restore', [UserController::class, 'restore'])->name('restore')->withTrashed();
    });
});
