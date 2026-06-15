<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Auth routes
Route::get('/signin', fn () => Inertia::render('Auth/Signin'))->name('signin');
Route::get('/signup', fn () => Inertia::render('Auth/Signup'))->name('signup');

// Dashboard / Home
Route::get('/', fn () => Inertia::render('Ecommerce'))->name('ecommerce');

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
