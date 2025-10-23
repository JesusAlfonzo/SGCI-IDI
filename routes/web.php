<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

// =================================================================
// RUTAS PROTEGIDAS: Solo un SUPER ADMINISTRADOR puede registrar nuevos usuarios
// =================================================================
Route::group(['middleware' => ['role:Super Administrador']], function () {
    // Estas son las rutas exactas que el comando Auth::routes() usa para el registro
    // Nota: Necesitas el controlador App\Http\Controllers\Auth\RegisterController.php
    Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
