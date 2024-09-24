<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use App\Models\Logistica\Kardex;
use DB;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public static function storeCompra($lstProductos,$registro_compra_id,$stock_previo,$stock_posterior){
        foreach ($lstProductos as $item) {
            $kardex                             =   new Kardex();
            $kardex->almacen_id                 =   $item->almacen_id;
            $kardex->producto_id                =   $item->producto_id;
            $kardex->cantidad                   =   $item->cantidad;
            $kardex->registro_compra_id         =   $registro_compra_id;
            $kardex->stock_previo               =   $stock_previo;
            $kardex->stock_posterior            =   $stock_posterior;
            $kardex->save();
        }
    }

    public static function storeSalida($lstProductos,$registro_salida,$stock_previo,$stock_posterior){
        foreach ($lstProductos as $item) {
            $kardex                             =   new Kardex();
            $kardex->almacen_id                 =   $registro_salida->almacen_origen_id;
            $kardex->producto_id                =   $item->producto_id;
            $kardex->cantidad                   =   $item->cantidad;
            $kardex->registro_salida_id         =   $registro_salida->id;
            $kardex->stock_previo               =   $stock_previo;
            $kardex->stock_posterior            =   $stock_posterior;
            $kardex->save();
        }
    }
}
