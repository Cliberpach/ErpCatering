<?php

namespace App\Http\Controllers\Kardex;

use App\Http\Controllers\Controller;
use App\Models\Kardex\Kardex;
use DB;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public static function storeCompra($producto,$registro_compra_id,$stock_previo,$stock_posterior){
            $kardex                             =   new Kardex();
            $kardex->almacen_id                 =   $producto->almacen_id;
            $kardex->producto_id                =   $producto->producto_id;
            $kardex->cantidad                   =   $producto->cantidad;
            $kardex->registro_compra_id         =   $registro_compra_id;
            $kardex->stock_previo               =   $stock_previo;
            $kardex->stock_posterior            =   $stock_posterior;
            $kardex->save();
        
    }

    public static function storeSalidaOrigen($producto,$registro_salida,$stock_previo,$stock_posterior){
            $kardex                             =   new Kardex();
            $kardex->almacen_id                 =   $registro_salida->almacen_origen_id;
            $kardex->producto_id                =   $producto->producto_id;
            $kardex->cantidad                   =   $producto->cantidad;
            $kardex->registro_salida_id         =   $registro_salida->id;
            $kardex->stock_previo               =   $stock_previo;
            $kardex->stock_posterior            =   $stock_posterior;
            $kardex->save();
    }

    public static function storeSalidaDestino($producto,$registro_salida,$stock_previo,$stock_posterior){
        $kardex                             =   new Kardex();
        $kardex->almacen_id                 =   $registro_salida->almacen_destino_id;
        $kardex->producto_id                =   $producto->producto_id;
        $kardex->cantidad                   =   $producto->cantidad;
        $kardex->registro_salida_id         =   $registro_salida->id;
        $kardex->stock_previo               =   $stock_previo;
        $kardex->stock_posterior            =   $stock_posterior;
        $kardex->save();
}
}
