<?php

use App\Http\Controllers\Herramientas\RolController;
use App\Http\Controllers\Registros\CategoriaController;
use App\Http\Controllers\Registros\ColaboradorController;
use App\Http\Controllers\Herramientas\UsuarioController;
use App\Http\Controllers\Registros\MarcaController;
use App\Http\Controllers\Utils\UtilController;
use App\Http\Middleware\CheckCustomPermission;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/acceso_denegado', function () {
    return view('reutilizables.pages.acceso_denegado');
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
    Route::put('/update/{id}', [UsuarioController::class, 'update'])->name('herramientas.usuario.update');
    Route::get('/getUsuarios', [UsuarioController::class, 'getUsuarios'])->name('herramientas.usuario.getUsuarios');
    Route::get('/edit/{id}', [UsuarioController::class, 'edit'])->name('herramientas.usuario.edit');
    Route::delete('/destroy/{id}', [UsuarioController::class, 'destroy'])->name('herramientas.usuario.destroy');

});

Route::group(['prefix' => 'roles', 'middleware' => ['auth']], function () {

    Route::get('/index', [RolController::class, 'index'])->name('herramientas.rol.index');
    Route::get('/create', [RolController::class, 'create'])->name('herramientas.rol.create');
    Route::post('/store', [RolController::class, 'store'])->name('herramientas.rol.store');
    Route::put('/update/{id}', [RolController::class, 'update'])->name('herramientas.rol.update');
    Route::get('/getRoles', [RolController::class, 'getRoles'])->name('herramientas.rol.getRoles');
    Route::get('/edit/{id}', [RolController::class, 'edit'])->name('herramientas.rol.edit');
    Route::delete('/destroy/{id}', [RolController::class, 'destroy'])->name('herramientas.rol.destroy');

});


//============ INICIO REGISTROS =================

Route::group(['prefix' => 'colaboradores', 'middleware' => ['auth','checkCustomPermission:registros.colaborador']], function () {

    Route::get('/index', [ColaboradorController::class, 'index'])->name('registros.colaborador.index');
    Route::get('/create', [ColaboradorController::class, 'create'])->name('registros.colaborador.create');
    Route::get('/edit/{id}', [ColaboradorController::class, 'edit'])->name('registros.colaborador.edit');
    Route::post('/store', [ColaboradorController::class, 'store'])->name('registros.colaborador.store');
    Route::put('/update/{id}', [ColaboradorController::class, 'update'])->name('registros.colaborador.update');
    Route::get('/consultarDni/{dni}', [ColaboradorController::class, 'consultarDni'])->name('registros.colaborador.consultarDni');
    Route::get('/getColaboradores', [ColaboradorController::class, 'getColaboradores'])->name('registros.colaborador.getColaboradores');
    Route::delete('/destroy/{id}', [ColaboradorController::class, 'destroy'])->name('registros.colaborador.destroy');

});


Route::group(['prefix' => 'marcas', 'middleware' => ['auth','checkCustomPermission:registros.marca']], function () {

    Route::get('/index', [MarcaController::class, 'index'])->name('registros.marca.index');
    Route::get('/create', [MarcaController::class, 'create'])->name('registros.marca.create');
    Route::post('/store', [MarcaController::class, 'store'])->name('registros.marca.store');
    Route::put('/update/{id}', [MarcaController::class, 'update'])->name('registros.marca.update');
    Route::get('/getMarcas', [MarcaController::class, 'getMarcas'])->name('registros.marca.getMarcas');
    Route::delete('/destroy/{id}', [MarcaController::class, 'destroy'])->name('registros.marca.destroy');

});

Route::group(['prefix' => 'categorias', 'middleware' => ['auth','checkCustomPermission:registros.categoria']], function () {

    Route::get('/index', [CategoriaController::class, 'index'])->name('registros.categoria.index');
    Route::get('/create', [CategoriaController::class, 'create'])->name('registros.categoria.create');
    Route::post('/store', [CategoriaController::class, 'store'])->name('registros.categoria.store');
    Route::put('/update/{id}', [CategoriaController::class, 'update'])->name('registros.categoria.update');
    Route::get('/getCategorias', [CategoriaController::class, 'getCategorias'])->name('registros.categoria.getCategorias');
    Route::delete('/destroy/{id}', [CategoriaController::class, 'destroy'])->name('registros.categoria.destroy');

});




//============= FIN REGISTROS ==========================

Route::group(['prefix' => 'utils', 'middleware' => ['auth']], function () {

    Route::get('/apiDni/{dni}', [UtilController::class, 'apiDni'])->name('utils.apiDni');
   
});


