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
Route::group(['prefix' => 'usuarios', 'middleware' => ['auth']], function () {

    Route::get('/index', [UsuarioController::class, 'index'])->name('herramientas.usuario.index');
    Route::get('/create', [UsuarioController::class, 'create'])->name('herramientas.usuario.create');
    Route::post('/store', [UsuarioController::class, 'store'])->name('herramientas.usuario.store');

});
