<?php

use App\Http\Controllers\Herramientas\RolController;
use App\Http\Controllers\Herramientas\TablaGeneralDetalleController;
use App\Http\Controllers\Jornales\RegistroLaborController;
use App\Http\Controllers\Registros\AlmacenController;
use App\Http\Controllers\Registros\CategoriaController;
use App\Http\Controllers\Registros\ColaboradorController;
use App\Http\Controllers\Herramientas\UsuarioController;
use App\Http\Controllers\Registros\MaquinariaController;
use App\Http\Controllers\Registros\MarcaController;
use App\Http\Controllers\Registros\ProductoController;
use App\Http\Controllers\Registros\ProyectoController;
use App\Http\Controllers\Utils\UtilController;
use App\Http\Middleware\CheckCustomPermission;
use App\Models\Herramientas\TablaGeneralDetalle;
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
    Route::get('/getListMarcas', [MarcaController::class, 'getListMarcas'])->name('registros.marca.getListMarcas');

});

Route::group(['prefix' => 'categorias', 'middleware' => ['auth','checkCustomPermission:registros.categoria']], function () {

    Route::get('/index', [CategoriaController::class, 'index'])->name('registros.categoria.index');
    Route::get('/create', [CategoriaController::class, 'create'])->name('registros.categoria.create');
    Route::post('/store', [CategoriaController::class, 'store'])->name('registros.categoria.store');
    Route::put('/update/{id}', [CategoriaController::class, 'update'])->name('registros.categoria.update');
    Route::get('/getCategorias', [CategoriaController::class, 'getCategorias'])->name('registros.categoria.getCategorias');
    Route::delete('/destroy/{id}', [CategoriaController::class, 'destroy'])->name('registros.categoria.destroy');
    Route::get('/getListCategorias', [CategoriaController::class, 'getListCategorias'])->name('registros.categoria.getListCategorias');

});

Route::group(['prefix' => 'productos', 'middleware' => ['auth','checkCustomPermission:registros.producto']], function () {

    Route::get('/index', [ProductoController::class, 'index'])->name('registros.producto.index');
    Route::get('/create', [ProductoController::class, 'create'])->name('registros.producto.create');
    Route::post('/store', [ProductoController::class, 'store'])->name('registros.producto.store');
    Route::get('/edit/{id}', [ProductoController::class, 'edit'])->name('registros.producto.edit');
    Route::put('/update/{id}', [ProductoController::class, 'update'])->name('registros.producto.update');
    Route::get('/getProductos', [ProductoController::class, 'getProductos'])->name('registros.producto.getProductos');
    Route::delete('/destroy/{id}', [ProductoController::class, 'destroy'])->name('registros.producto.destroy');

});

Route::group(['prefix' => 'maquinarias', 'middleware' => ['auth','checkCustomPermission:registros.maquinaria']], function () {

    Route::get('/index', [MaquinariaController::class, 'index'])->name('registros.maquinaria.index');
    Route::get('/create', [MaquinariaController::class, 'create'])->name('registros.maquinaria.create');
    Route::post('/store', [MaquinariaController::class, 'store'])->name('registros.maquinaria.store');
    Route::get('/edit/{id}', [MaquinariaController::class, 'edit'])->name('registros.maquinaria.edit');
    Route::put('/update/{id}', [MaquinariaController::class, 'update'])->name('registros.maquinaria.update');
    Route::get('/getMaquinarias', [MaquinariaController::class, 'getMaquinarias'])->name('registros.maquinaria.getMaquinarias');
    Route::delete('/destroy/{id}', [MaquinariaController::class, 'destroy'])->name('registros.maquinaria.destroy');

});


Route::group(['prefix' => 'almacenes', 'middleware' => ['auth','checkCustomPermission:registros.almacen']], function () {

    Route::get('/index', [AlmacenController::class, 'index'])->name('registros.almacen.index');
    Route::get('/create', [AlmacenController::class, 'create'])->name('registros.almacen.create');
    Route::post('/store', [AlmacenController::class, 'store'])->name('registros.almacen.store');
    Route::put('/update/{id}', [AlmacenController::class, 'update'])->name('registros.almacen.update');
    Route::get('/getAlmacenes', [AlmacenController::class, 'getAlmacenes'])->name('registros.almacen.getAlmacenes');
    Route::delete('/destroy/{id}', [AlmacenController::class, 'destroy'])->name('registros.almacen.destroy');
    Route::get('/getListAlmacenes', [AlmacenController::class, 'getListAlmacenes'])->name('registros.almacen.getListAlmacenes');
    Route::patch('/asignarProyecto/{id}', [AlmacenController::class, 'asignarProyecto'])->name('registros.almacen.asignarProyecto');

});

Route::group(['prefix' => 'proyectos', 'middleware' => ['auth','checkCustomPermission:registros.proyecto']], function () {

    Route::get('/index', [ProyectoController::class, 'index'])->name('registros.proyecto.index');
    Route::get('/create', [ProyectoController::class, 'create'])->name('registros.proyecto.create');
    Route::post('/store', [ProyectoController::class, 'store'])->name('registros.proyecto.store');
    Route::get('/edit/{id}', [ProyectoController::class, 'edit'])->name('registros.proyecto.edit');
    Route::put('/update/{id}', [ProyectoController::class, 'update'])->name('registros.proyecto.update');
    Route::get('/getProyectos', [ProyectoController::class, 'getProyectos'])->name('registros.proyecto.getProyectos');
    Route::delete('/destroy/{id}', [ProyectoController::class, 'destroy'])->name('registros.proyecto.destroy');
    Route::patch('/asignarSupervisor/{id}', [ProyectoController::class, 'asignarSupervisor'])->name('registros.proyecto.asignarSupervisor');

});


Route::group(['prefix' => 'tablas_generales_detalles', 'middleware' => ['auth','checkCustomPermission:herramientas.tabla_general']], function () {

    // Route::get('/index', [TablaGeneralDetalle::class, 'index'])->name('registros.maquinaria.index');
    // Route::get('/create', [MaquinariaController::class, 'create'])->name('registros.maquinaria.create');
    Route::post('/store', [TablaGeneralDetalleController::class, 'store'])->name('herramientas.tabla_general_detalle.store');
    // Route::get('/edit/{id}', [MaquinariaController::class, 'edit'])->name('registros.maquinaria.edit');
    // Route::put('/update/{id}', [MaquinariaController::class, 'update'])->name('registros.maquinaria.update');
    Route::get('/getListTablaGeneralDetalles/{id}', [TablaGeneralDetalleController::class, 'getListTablaGeneralDetalles'])->name('registros.tabla_general_detalle.getListTablaGeneralDetalles');
    // Route::delete('/destroy/{id}', [MaquinariaController::class, 'destroy'])->name('registros.maquinaria.destroy');

});

//============= FIN REGISTROS ==========================

//========= INICIO JORNALES =========

Route::group(['prefix' => 'jornales', 'middleware' => ['auth','checkCustomPermission:jornal.registro_labor']], function () {

    Route::get('/index', [RegistroLaborController::class, 'index'])->name('jornales.registro_labor.index');
    // Route::get('/create', [ProyectoController::class, 'create'])->name('registros.proyecto.create');
    Route::post('/store', [RegistroLaborController::class, 'store'])->name('jornales.registro_labor.store');
    // Route::get('/edit/{id}', [ProyectoController::class, 'edit'])->name('registros.proyecto.edit');
    // Route::put('/update/{id}', [ProyectoController::class, 'update'])->name('registros.proyecto.update');
    Route::get('/getRegistrosLabor', [RegistroLaborController::class, 'getRegistrosLabor'])->name('jornales.registro_labor.getRegistrosLabor');
    // Route::delete('/destroy/{id}', [ProyectoController::class, 'destroy'])->name('registros.proyecto.destroy');

});





//=========== FIN JORNALES ==========

Route::group(['prefix' => 'utils', 'middleware' => ['auth']], function () {

    Route::get('/apiDni/{dni}', [UtilController::class, 'apiDni'])->name('utils.apiDni');
   
});


