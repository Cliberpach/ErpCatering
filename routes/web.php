<?php

use App\Http\Controllers\Herramientas\ColaboradorController;
use App\Http\Controllers\Herramientas\UsuarioController;
use App\Http\Controllers\Utils\UtilController;
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

Route::group(['prefix' => 'usuarios', 'middleware' => ['auth']], function () {

    Route::get('/index', [UsuarioController::class, 'index'])->name('herramientas.usuario.index');
    Route::get('/create', [UsuarioController::class, 'create'])->name('herramientas.usuario.create');
    Route::post('/store', [UsuarioController::class, 'store'])->name('herramientas.usuario.store');

});

Route::group(['prefix' => 'colaboradores', 'middleware' => ['auth']], function () {

    Route::get('/index', [ColaboradorController::class, 'index'])->name('herramientas.colaborador.index');
    Route::get('/create', [ColaboradorController::class, 'create'])->name('herramientas.colaborador.create');
    Route::get('/edit/{id}', [ColaboradorController::class, 'edit'])->name('herramientas.colaborador.edit');
    Route::post('/store', [ColaboradorController::class, 'store'])->name('herramientas.colaborador.store');
    Route::put('/update/{id}', [ColaboradorController::class, 'update'])->name('herramientas.colaborador.update');
    Route::get('/consultarDni/{dni}', [ColaboradorController::class, 'consultarDni'])->name('herramientas.colaborador.consultarDni');
    Route::get('/getColaboradores', [ColaboradorController::class, 'getColaboradores'])->name('herramientas.colaborador.getColaboradores');
    Route::delete('/destroy/{id}', [ColaboradorController::class, 'destroy'])->name('herramientas.colaborador.destroy');

});

Route::group(['prefix' => 'utils', 'middleware' => ['auth']], function () {

    Route::get('/apiDni/{dni}', [UtilController::class, 'apiDni'])->name('utils.apiDni');
   

});


