<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
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
});
