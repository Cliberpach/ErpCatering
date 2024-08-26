<?php

use App\Http\Controllers\Herramientas\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('layouts.layout');
    })->name('dashboard');
});

// Grupo de rutas con un prefijo y middleware opcional
Route::group(['prefix' => 'herramientas', 'middleware' => ['auth']], function () {

    Route::get('/usuario', [UsuarioController::class, 'index'])->name('herramientas.usuario.index');

});
