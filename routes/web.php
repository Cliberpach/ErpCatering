<?php

use App\Http\Controllers\Compras\ProveedorController;
use App\Http\Controllers\Consultas\CMaquinariaController;
use App\Http\Controllers\Consultas\CPersonalController;
use App\Http\Controllers\Consultas\CProductoController;
use App\Http\Controllers\Herramientas\EmpresaController;
use App\Http\Controllers\Herramientas\RolController;
use App\Http\Controllers\Herramientas\TablaGeneralDetalleController;
use App\Http\Controllers\Jornales\RegistroLaborController;
use App\Http\Controllers\Compras\CotizacionCompraController;
use App\Http\Controllers\Compras\OrdenCompraController;
use App\Http\Controllers\Compras\RegistroCompraController;
use App\Http\Controllers\Logistica\RegistroSalidaController;
use App\Http\Controllers\PlanProyecto\TareaController;
use App\Http\Controllers\Registros\AlmacenController;
use App\Http\Controllers\Registros\CargoController;
use App\Http\Controllers\Registros\CategoriaController;
use App\Http\Controllers\Registros\ColaboradorController;
use App\Http\Controllers\Herramientas\UsuarioController;
use App\Http\Controllers\Logistica\ListaRequerimientoController;
use App\Http\Controllers\Requerimientos\RequerimientoController;
use App\Http\Controllers\Registros\MaquinariaController;
use App\Http\Controllers\Registros\MarcaController;
use App\Http\Controllers\Registros\ModalidadPagoController;
use App\Http\Controllers\Registros\ProductoController;
use App\Http\Controllers\Registros\ProyectoController;
use App\Http\Controllers\TrabajoEquipo\RegistroTareaController;
use App\Http\Controllers\Utils\UtilController;
use App\Http\Middleware\CheckCustomPermission;
use App\Models\Herramientas\Empresa;
use App\Models\Herramientas\TablaGeneralDetalle;
use App\Models\Logistica\CotizacionCompra;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $empresa = Empresa::first(); 
    return view('auth.login', ['empresa' => $empresa]);});

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
    Route::get('/getSupervisores', [ColaboradorController::class, 'getSupervisores'])->name('registros.colaborador.getSupervisores');

});

Route::group(['prefix' => 'cargos', 'middleware' => ['auth','checkCustomPermission:registros.cargo']], function () {

    Route::get('/index', [CargoController::class, 'index'])->name('registros.cargo.index');
    // Route::get('/create', [ColaboradorController::class, 'create'])->name('registros.colaborador.create');
    // Route::get('/edit/{id}', [ColaboradorController::class, 'edit'])->name('registros.colaborador.edit');
    Route::post('/store', [CargoController::class, 'store'])->name('registros.cargo.store');
    Route::put('/update/{id}', [CargoController::class, 'update'])->name('registros.cargo.update');
    // Route::get('/consultarDni/{dni}', [ColaboradorController::class, 'consultarDni'])->name('registros.colaborador.consultarDni');
    Route::get('/getCargos', [CargoController::class, 'getCargos'])->name('registros.cargo.getCargos');
    Route::delete('/destroy/{id}', [CargoController::class, 'destroy'])->name('registros.cargo.destroy');

});


Route::group(['prefix' => 'marcas', 'middleware' => ['auth','checkCustomPermission:registros.marca']], function () {

    Route::get('/index', [MarcaController::class, 'index'])->name('registros.marca.index');
    Route::get('/create', [MarcaController::class, 'create'])->name('registros.marca.create');
    Route::post('/store', [MarcaController::class, 'store'])->name('registros.marca.store');
    Route::put('/update/{id}', [MarcaController::class, 'update'])->name('registros.marca.update');
    Route::get('/getMarcas', [MarcaController::class, 'getMarcas'])->name('registros.marca.getMarcas');
    Route::delete('/destroy/{id}', [MarcaController::class, 'destroy'])->name('registros.marca.destroy');
    Route::get('/getListMarcas', [MarcaController::class, 'getListMarcas'])->name('registros.marca.getListMarcas');
    Route::get('/descargarFormatoExcel', [MarcaController::class, 'descargarFormatoExcel'])->name('registros.marca.descargarFormatoExcel');
    Route::post('/importarMarcasExcel', [MarcaController::class, 'importarMarcasExcel'])->name('registros.marca.importarMarcasExcel');

});

Route::group(['prefix' => 'categorias', 'middleware' => ['auth','checkCustomPermission:registros.categoria']], function () {

    Route::get('/index', [CategoriaController::class, 'index'])->name('registros.categoria.index');
    Route::get('/create', [CategoriaController::class, 'create'])->name('registros.categoria.create');
    Route::post('/store', [CategoriaController::class, 'store'])->name('registros.categoria.store');
    Route::put('/update/{id}', [CategoriaController::class, 'update'])->name('registros.categoria.update');
    Route::get('/getCategorias', [CategoriaController::class, 'getCategorias'])->name('registros.categoria.getCategorias');
    Route::delete('/destroy/{id}', [CategoriaController::class, 'destroy'])->name('registros.categoria.destroy');
    Route::get('/getListCategorias', [CategoriaController::class, 'getListCategorias'])->name('registros.categoria.getListCategorias');
    Route::get('/descargarFormatoExcel', [CategoriaController::class, 'descargarFormatoExcel'])->name('registros.categoria.descargarFormatoExcel');
    Route::post('/importarCategoriasExcel', [CategoriaController::class, 'importarCategoriasExcel'])->name('registros.categoria.importarCategoriasExcel');

});

Route::group(['prefix' => 'productos', 'middleware' => ['auth','checkCustomPermission:registros.producto']], function () {

    Route::get('/index', [ProductoController::class, 'index'])->name('registros.producto.index');
    Route::get('/create', [ProductoController::class, 'create'])->name('registros.producto.create');
    Route::post('/store', [ProductoController::class, 'store'])->name('registros.producto.store');
    Route::get('/edit/{id}', [ProductoController::class, 'edit'])->name('registros.producto.edit');
    Route::put('/update/{id}', [ProductoController::class, 'update'])->name('registros.producto.update');
    Route::get('/getProductos', [ProductoController::class, 'getProductos'])->name('registros.producto.getProductos');
    Route::get('/getProductosByAlmacen', [ProductoController::class, 'getProductosByAlmacen'])->name('registros.producto.getProductosByAlmacen');
    Route::delete('/destroy/{id}', [ProductoController::class, 'destroy'])->name('registros.producto.destroy');
    Route::get('/show/{id}', [ProductoController::class, 'show'])->name('registros.producto.show');
    Route::get('/descargarFormatoExcel', [ProductoController::class, 'descargarFormatoExcel'])->name('registros.producto.descargarFormatoExcel');
    Route::post('/importarProductosExcel', [ProductoController::class, 'importarProductosExcel'])->name('registros.producto.importarProductosExcel');
    Route::get('/excel', [ProductoController::class, 'excel'])->name('registros.producto.excel');

});

Route::group(['prefix' => 'modalidad_pago', 'middleware' => ['auth','checkCustomPermission:registros.modalidad_pago']], function () {

    Route::get('/index', [ModalidadPagoController::class, 'index'])->name('registros.modalidad_pago.index');
    Route::get('/create', [ModalidadPagoController::class, 'create'])->name('registros.modalidad_pago.create');
    Route::post('/store', [ModalidadPagoController::class, 'store'])->name('registros.modalidad_pago.store');
    Route::get('getModalidadesPago', [ModalidadPagoController::class, 'getModalidadesPago'])->name('registros.modalidad_pago.getModalidadesPago');
    Route::put('/update/{id}', [ModalidadPagoController::class, 'update'])->name('registros.modalidad_pago.update');
    Route::get('/edit/{id}', [ModalidadPagoController::class, 'edit'])->name('registros.modalidad_pago.edit');
    Route::delete('/destroy/{id}', [ModalidadPagoController::class, 'destroy'])->name('registros.modalidad_pago.destroy');

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
    Route::patch('/finalizarProyecto/{id}', [ProyectoController::class, 'finalizarProyecto'])->name('registros.proyecto.finalizarProyecto');
    
    Route::get('/asignarPersonal/{id}', [ProyectoController::class, 'asignarPersonalCreate'])->name('registros.proyecto.asignarPersonalCreate');
    Route::post('/asignarPersonal', [ProyectoController::class, 'asignarPersonalStore'])->name('registros.proyecto.asignarPersonalStore');

    Route::get('/asignarMaquinaria/{id}', [ProyectoController::class, 'asignarMaquinariaCreate'])->name('registros.proyecto.asignarMaquinariaCreate');
    Route::post('/asignarMaquinaria', [ProyectoController::class, 'asignarMaquinariaStore'])->name('registros.proyecto.asignarMaquinariaStore');

    Route::get('/show/{id}', [ProyectoController::class, 'show'])->name('registros.proyecto.show');

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
    Route::post('/store', [RegistroLaborController::class, 'store'])->name('jornales.registro_labor.store');
    // Route::get('/edit/{id}', [ProyectoController::class, 'edit'])->name('registros.proyecto.edit');
    Route::put( '/finalizar/{id}', [RegistroLaborController::class, 'finalizar'])->name('jornales.registro_labor.finalizar');
    Route::get('/getRegistrosLabor', [RegistroLaborController::class, 'getRegistrosLabor'])->name('jornales.registro_labor.getRegistrosLabor');
    Route::delete('/destroy/{id}', [RegistroLaborController::class, 'destroy'])->name('jornales.registro_labor.destroy');
    Route::get('/asistencias/{id}', [RegistroLaborController::class, 'asistenciasCreate'])->name('jornales.registro_labor.asistenciasCreate');
    Route::post('/marcarEntrada', [RegistroLaborController::class, 'marcarEntrada'])->name('jornales.registro_labor.marcarEntrada');
    Route::post('/marcarSalida', [RegistroLaborController::class, 'marcarSalida'])->name('jornales.registro_labor.marcarSalida');

});

//=========== FIN JORNALES ==========


//============ INICIO TRABAJO EQUIPOS =======
Route::group(['prefix' => 'trabajo_equipos', 'middleware' => ['auth','checkCustomPermission:trabajo_equipo.registro_tarea']], function () {

    Route::get('/index', [RegistroTareaController::class, 'index'])->name('trabajo_equipos.registro_tarea.index');
    Route::post('/store', [RegistroTareaController::class, 'store'])->name('trabajo_equipos.registro_tarea.store');
    Route::get('/edit/{id}', [RegistroTareaController::class, 'edit'])->name('trabajo_equipos.registro_tarea.edit');
    Route::put('/update/{id}', [RegistroTareaController::class, 'update'])->name('trabajo_equipos.registro_tarea.update');
    Route::get('/create', [RegistroTareaController::class, 'create'])->name('trabajo_equipos.registro_tarea.create');
    Route::get('/getRegistrosTarea', [RegistroTareaController::class, 'getRegistrosTarea'])->name('trabajo_equipos.registro_tarea.getRegistrosTarea');
    Route::delete('/destroy/{id}', [RegistroTareaController::class, 'destroy'])->name('trabajo_equipos.registro_tarea.destroy');
    //Route::get('/asistencias/{id}', [RegistroLaborController::class, 'asistenciasCreate'])->name('jornales.registro_labor.asistenciasCreate');
    //Route::post('/marcarEntrada', [RegistroLaborController::class, 'marcarEntrada'])->name('jornales.registro_labor.marcarEntrada');
    //Route::post('/marcarSalida', [RegistroLaborController::class, 'marcarSalida'])->name('jornales.registro_labor.marcarSalida');

});

//============= FIN TRABAJO EQUIPOS ===========


//========== INICIO REQUERIMIENTOS ==========

Route::group(['prefix' => 'requerimientos', 'middleware' => ['auth','checkCustomPermission:requerimientos.requerimientos']], function () {

    Route::get('/index', [RequerimientoController::class, 'index'])->name('requerimientos.requerimientos.index');
    Route::get('/create', [RequerimientoController::class, 'create'])->name('requerimientos.requerimientos.create')->middleware('checkRole:SUPERVISOR');
    Route::get('/getRequerimientos', [RequerimientoController::class, 'getRequerimientos'])->name('requerimientos.requerimientos.getRequerimientos');
    Route::put('/update/{id}', [RequerimientoController::class, 'update'])->name('requerimientos.requerimientos.update');
    Route::post('/store', [RequerimientoController::class, 'store'])->name('requerimientos.requerimientos.store')->middleware('checkRole:SUPERVISOR');
    Route::get('/edit/{id}', [RequerimientoController::class, 'edit'])->name('requerimientos.requerimientos.edit')->middleware('checkRole:SUPERVISOR');
    Route::delete('/destroy/{id}', [RequerimientoController::class, 'destroy'])->name('requerimientos.requerimientos.destroy');
    Route::get('/show/{id}', [RequerimientoController::class, 'show'])->name('requerimientos.requerimientos.show');
    Route::get('/getProductos', [RequerimientoController::class, 'getProductos'])->name('requerimientos.requerimientos.getProductos');

});

//========= FIN REQUERIMIENTOS ==========


//============ INICIO LOGÍSTICA =======
Route::group(['prefix' => 'registro_salida', 'middleware' => ['auth','checkCustomPermission:logistica.registro_salida']], function () {

    Route::get('/index', [RegistroSalidaController::class, 'index'])->name('logistica.registro_salida.index');
    Route::get('/create', [RegistroSalidaController::class, 'create'])->name('logistica.registro_salida.create');
    Route::get('/getSalidas', [RegistroSalidaController::class, 'getSalidas'])->name('logistica.registro_salida.getSalidas');
    Route::get('/validarCantidad/{almacen_id}/{producto_id}/{cantidad}', [RegistroSalidaController::class, 'validarCantidad'])->name('logistica.registro_salida.validarCantidad');
    Route::post('/store', [RegistroSalidaController::class, 'store'])->name('logistica.registro_salida.store');
    Route::get('/show/{salida_id}', [RegistroSalidaController::class, 'show'])->name('logistica.registro_salida.show');
    Route::get('/pdf/{id}', [RegistroSalidaController::class, 'pdf'])->name('logistica.registro_salida.pdf');

});

Route::group(['prefix' => 'lista_requerimientos', 'middleware' => ['auth','checkCustomPermission:logistica.lista_requerimientos']], function () {

    Route::get('/index', [ListaRequerimientoController::class, 'index'])->name('logistica.lista_requerimientos.index');
    Route::post('/generarCotizacion', [ListaRequerimientoController::class, 'generarCotizacion'])->name('logistica.lista_requerimientos.generarCotizacion');
    Route::get('/getRequerimientos', [ListaRequerimientoController::class, 'getRequerimientos'])->name('logistica.lista_requerimientos.getRequerimientos');
    Route::get('/show/{id}', [ListaRequerimientoController::class, 'show'])->name('logistica.lista_requerimientos.show');

    // Route::get('/create', [RequerimientoController::class, 'create'])->name('requerimientos.requerimientos.create')->middleware('checkRole:SUPERVISOR');
    // Route::get('/getRequerimientos', [RequerimientoController::class, 'getRequerimientos'])->name('requerimientos.requerimientos.getRequerimientos');
    // Route::put('/update/{id}', [RequerimientoController::class, 'update'])->name('requerimientos.requerimientos.update');
    // Route::post('/store', [RequerimientoController::class, 'store'])->name('requerimientos.requerimientos.store')->middleware('checkRole:SUPERVISOR');
    // Route::get('/edit/{id}', [RequerimientoController::class, 'edit'])->name('requerimientos.requerimientos.edit')->middleware('checkRole:SUPERVISOR');
    // Route::delete('/destroy/{id}', [RequerimientoController::class, 'destroy'])->name('requerimientos.requerimientos.destroy');
    // Route::get('/show/{id}', [RequerimientoController::class, 'show'])->name('requerimientos.requerimientos.show');

});

//============= FIN LOGÍSTICA ===========


//======== INICIO COMPRAS =======
Route::group(['prefix' => 'cotizacion_compra', 'middleware' => ['auth','checkCustomPermission:compras.cotizacion_compra']], function () {

    Route::get('/index', [CotizacionCompraController::class, 'index'])->name('compras.cotizacion_compra.index');
    Route::post('/store', [CotizacionCompraController::class, 'store'])->name('compras.cotizacion_compra.store');
    Route::get('/edit/{id}', [CotizacionCompraController::class, 'edit'])->name('compras.cotizacion_compra.edit');
    Route::put('/update/{id}', [CotizacionCompraController::class, 'update'])->name('compras.cotizacion_compra.update');
    Route::get('/create', [CotizacionCompraController::class, 'create'])->name('compras.cotizacion_compra.create');
    Route::delete('/destroy/{id}', [CotizacionCompraController::class, 'destroy'])->name('compras.cotizacion_compra.destroy');
    Route::get('/getCotizacionesCompra', [CotizacionCompraController::class, 'getCotizacionesCompra'])->name('compras.cotizacion_compra.getCotizacionesCompra');
    Route::get('/pdf/{id}', [CotizacionCompraController::class, 'pdf'])->name('compras.cotizacion_compra.pdf');
    Route::get('/goToOrdenCompra/{id}', [CotizacionCompraController::class, 'goToOrdenCompra'])->name('compras.cotizacion_compra.goToOrdenCompra');
    Route::post('/cotizacionToOrden', [CotizacionCompraController::class, 'cotizacionToOrden'])->name('compras.cotizacion_compra.cotizacionToOrden');

});

Route::group(['prefix' => 'registro_compra', 'middleware' => ['auth','checkCustomPermission:compras.registro_compra']], function () {

    Route::get('/index', [RegistroCompraController::class, 'index'])->name('compras.registro_compra.index');
    Route::post('/store', [RegistroCompraController::class, 'store'])->name('compras.registro_compra.store');
    Route::get('/create', [RegistroCompraController::class, 'create'])->name('compras.registro_compra.create');
    Route::get('/show/{id}', [RegistroCompraController::class, 'show'])->name('compras.registro_compra.show');
    Route::get('/getCompras', [RegistroCompraController::class, 'getCompras'])->name('compras.registro_compra.getCompras');
    Route::get('/pdf/{id}', [RegistroCompraController::class, 'pdf'])->name('compras.registro_compra.pdf');

});

Route::group(['prefix' => 'orden_compra', 'middleware' => ['auth','checkCustomPermission:compras.orden_compra']], function () {

    Route::get('/index', [OrdenCompraController::class, 'index'])->name('compras.orden_compra.index');
    // Route::post('/store', [RegistroCompraController::class, 'store'])->name('compras.registro_compra.store');
    Route::get('/create', [OrdenCompraController::class, 'create'])->name('compras.orden_compra.create');
    // Route::get('/show/{id}', [RegistroCompraController::class, 'show'])->name('compras.registro_compra.show');
    Route::get('/getOrdenesCompra', [OrdenCompraController::class, 'getOrdenesCompra'])->name('compras.orden_compra.getOrdenesCompra');
    Route::get('/pdf/{id}', [OrdenCompraController::class, 'pdf'])->name('compras.orden_compra.pdf');

});

Route::group(['prefix' => 'proveedores', 'middleware' => 'auth'], function () {
    Route::get('/index', [ProveedorController::class, 'index'])->middleware('checkCustomPermission:compras.proveedor')->name('compras.proveedor.index');
    Route::post('/store', [ProveedorController::class, 'store'])->middleware('checkCustomPermission:compras.proveedor')->name('compras.proveedor.store');
    Route::get('/create', [ProveedorController::class, 'create'])->middleware('checkCustomPermission:compras.proveedor')->name('compras.proveedor.create');
    Route::get('/edit/{id}', [ProveedorController::class, 'edit'])->middleware('checkCustomPermission:compras.proveedor')->name('compras.proveedor.edit');
    Route::put('/update/{id}', [ProveedorController::class, 'update'])->middleware('checkCustomPermission:compras.proveedor')->name('compras.proveedor.update');
    Route::delete('/destroy/{id}', [ProveedorController::class, 'destroy'])->middleware('checkCustomPermission:compras.proveedor')->name('compras.proveedor.destroy');
    Route::get('/getProveedores', [ProveedorController::class, 'getProveedores'])->middleware('checkCustomPermission:compras.proveedor')->name('compras.proveedor.getProveedores');
    Route::get('/consultarDocumento', [ProveedorController::class, 'consultarDocumento'])->name('compras.proveedor.consultarDocumento');
    Route::get('/getListProveedores', [ProveedorController::class, 'getListProveedores'])->middleware('checkCustomPermission:compras.proveedor')->name('compras.proveedor.getListProveedores');
});

//====== FIN COMPRAS =========


//============== INICIO PLAN PROYECTO ==========

Route::group(['prefix' => 'plan_proyecto', 'middleware' => ['auth','checkCustomPermission:plan_proyecto.tarea']], function () {

    Route::get('/index', [TareaController::class, 'index'])->name('plan_proyecto.tarea.index');
    Route::get('/create/{id}', [TareaController::class, 'create'])->name('plan_proyecto.tarea.create');
    Route::post('/store', [TareaController::class, 'store'])->name('plan_proyecto.tarea.store');
    Route::get('/getTareas', [TareaController::class, 'getTareas'])->name('plan_proyecto.tarea.getTareas');
    Route::get('/show/{tarea_id}', [TareaController::class, 'show'])->name('plan_proyecto.tarea.show');
    Route::get('/edit/{id}', [TareaController::class, 'edit'])->name('plan_proyecto.tarea.edit');
    Route::put('/update/{id}', [TareaController::class, 'update'])->name('plan_proyecto.tarea.update');
    Route::put('/avance/{id}', [TareaController::class, 'avance'])->name('plan_proyecto.tarea.avance');
    // Route::delete('/destroy/{id}', [MaquinariaController::class, 'destroy'])->name('registros.maquinaria.destroy');

});

//=========== FIN PLAN PROYECTO ============

//======== INICIO HERRAMIENTAS ===========//
Route::group(['prefix' => 'usuarios', 'middleware' => ['auth']], function () {

    Route::get('/index', [UsuarioController::class, 'index'])->name('herramientas.usuario.index');
    Route::get('/create', [UsuarioController::class, 'create'])->name('herramientas.usuario.create');
    Route::post('/store', [UsuarioController::class, 'store'])->name('herramientas.usuario.store');
    Route::put('/update/{id}', [UsuarioController::class, 'update'])->name('herramientas.usuario.update');
    Route::get('/getUsuarios', [UsuarioController::class, 'getUsuarios'])->name('herramientas.usuario.getUsuarios');
    Route::get('/edit/{id}', [UsuarioController::class, 'edit'])->name('herramientas.usuario.edit');
    Route::delete('/destroy/{id}', [UsuarioController::class, 'destroy'])->name('herramientas.usuario.destroy');
    Route::get('/getSupervisores', [UsuarioController::class, 'getSupervisores'])->name('herramientas.usuario.getSupervisores');

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

Route::group(['prefix' => 'empresa', 'middleware' => ['auth']], function () {

    Route::get('/index', [EmpresaController::class, 'index'])->name('herramientas.empresa.index');
    Route::get('/consultarDocumento', [EmpresaController::class, 'consultarDocumento'])->name('herramientas.empresa.consultarDocumento');
    Route::put('/{id}', [EmpresaController::class, 'update'])->name('herramientas.empresa.update');
    // Route::put('/update/{id}', [RolController::class, 'update'])->name('herramientas.rol.update');
    // Route::get('/getRoles', [RolController::class, 'getRoles'])->name('herramientas.rol.getRoles');
    // Route::get('/edit/{id}', [RolController::class, 'edit'])->name('herramientas.rol.edit');
    // Route::delete('/destroy/{id}', [RolController::class, 'destroy'])->name('herramientas.rol.destroy');

});
//======= FIN HERRAMIENTAS ==========


//========== INICIO CONSULTAS ===========

Route::group(['prefix' => 'consultas_personal', 'middleware' => ['auth','checkCustomPermission:consultas.personal']], function () {

    Route::get('/index', [CPersonalController::class, 'index'])->name('consultas.personal.index');
    Route::get('/getConsultaPersonal', [CPersonalController::class, 'getConsultaPersonal'])->name('consultas.personal.getConsultaPersonal');
    Route::get('/excel', [CPersonalController::class, 'excel'])->name('consultas.personal.excel');
    Route::get('/pdf', [CPersonalController::class, 'pdf'])->name('consultas.personal.pdf');
 
});

Route::group(['prefix' => 'consultas_maquinaria', 'middleware' => ['auth','checkCustomPermission:consultas.maquinaria']], function () {

    Route::get('/index', [CMaquinariaController::class, 'index'])->name('consultas.maquinaria.index');
    Route::get('/getConsultaMaquinaria', [CMaquinariaController::class, 'getConsultaMaquinaria'])->name('consultas.maquinaria.getConsultaMaquinaria');
    Route::get('/excel', [CMaquinariaController::class, 'excel'])->name('consultas.maquinaria.excel');
    Route::get('/pdf', [CMaquinariaController::class, 'pdf'])->name('consultas.maquinaria.pdf');
 
});

Route::group(['prefix' => 'consultas_producto', 'middleware' => ['auth','checkCustomPermission:consultas.producto']], function () {

    Route::get('/index', [CProductoController::class, 'index'])->name('consultas.producto.index');
    Route::get('/getConsultaProducto', [CProductoController::class, 'getConsultaProducto'])->name('consultas.producto.getConsultaProducto');
    Route::get('/excel', [CProductoController::class, 'excel'])->name('consultas.producto.excel');
    Route::get('/pdf', [CProductoController::class, 'pdf'])->name('consultas.producto.pdf');
 
});


//======= FIN CONSULTAS ============

Route::group(['prefix' => 'utils', 'middleware' => ['auth']], function () {

    Route::get('/apiDni/{dni}', [UtilController::class, 'apiDni'])->name('utils.apiDni');
    Route::get( '/tipoCambio', [UtilController::class, 'tipoCambio'])->name('utils.tipoCambio');

});


