<?php

namespace App\Http\Controllers\Registros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registros\Platos\PlatoRequest;
use App\Models\Registros\Plato;
use App\Models\Registros\PlatoDetalle;
use App\Models\Registros\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PlatoController extends Controller
{
    public function index()
    {
        return view('registros.platos.index');
    }

    public function getPlatos()
    {
        $platos = Plato::where('estado', 'ACTIVO')
            ->select(
                'id',
                'nombre',
                'calorias',
                'proteinas',
                'carbohidratos',
                'grasas',
                'peso',
                'costo'
            )
            ->get();

        return DataTables::of($platos)
            ->make(true);
    }

    public function getDetallePlatos($id)
    {
        $composicion = DB::table('plato_detalles as pd')
            ->join('productos as p', 'pd.producto_id', '=', 'p.id')
            ->where('pd.plato_id', $id)
            ->select('p.nombre as producto', 'pd.cantidad', 'pd.unidad_medida')
            ->get();

        return DataTables::of($composicion)
            ->make(true);
    }

    public function store(PlatoRequest $request)
    {
        try {
            $plato = Plato::create([
                'nombre' => $request->input('nombre'),
                'calorias' => $request->input('calorias'),
                'proteinas' => $request->input('proteinas'),
                'carbohidratos' => $request->input('carbohidratos'),
                'grasas' => $request->input('grasas'),
                'peso' => $request->input('peso'),
                'costo' => $request->input('costo'),
            ]);

            // Retornar respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'El plato se ha registrado correctamente.',
            ], 200);
        } catch (\Exception $e) {
            // En caso de error al guardar
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar el plato.',
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $plato = Plato::findOrFail($id);

            return response()->json([
                'success' => true,
                'plato' => $plato,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Plato no encontrado.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ocurrió un error inesperado.',
            ], 500);
        }
    }

    public function update(PlatoRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $plato = Plato::find($id);

            if (!$plato) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plato no encontrado.',
                ], 404);
            }

            $plato->nombre = Str::upper($request->get('nombre'));
            $plato->calorias = $request->get('calorias');
            $plato->proteinas = $request->get('proteinas');
            $plato->carbohidratos = $request->get('carbohidratos');
            $plato->grasas = $request->get('grasas');
            $plato->peso = $request->get('peso');
            $plato->costo = $request->get('costo');

            $plato->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plato actualizado correctamente.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el plato: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function getProductos(Request $request)
    {
        try {
            // Obtén los productos que son activos y del tipo INSUMO
            $productos = Producto::where('productos.estado', 'ACTIVO')
                ->where('productos.tipo_producto', 'INSUMO')
                ->join('tablas_generales_detalles as tgd', 'tgd.id', '=', 'productos.unidad_medida_id') // Relaciona con la tabla de unidades
                ->get(['productos.id', 'productos.nombre', 'productos.unidad_medida_id', 'tgd.simbolo as unidad_medida']);

            if ($productos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron productos.',
                ], 404); // Si no hay productos, devuelve un error
            }

            return response()->json([
                'success' => true,
                'products' => $productos,
            ], 200);
        } catch (\Exception $e) {
            // Si ocurre un error en la consulta, captura la excepción
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos: ' . $e->getMessage(),
            ], 500); // Devuelve el error con el código 500
        }
    }

    public function guardarComposicion(Request $request)
    {
        try {
            // Validación de los datos recibidos
            $validated = $request->validate([
                'plato_id' => 'required|exists:platos,id',
                'producto_id' => 'required|exists:productos,id',
                'cantidad' => 'required|numeric|min:1',
                'unidad_medida' => 'required|string',
            ]);

            $producto = Producto::find($validated['producto_id']);
            $productoNombre = $producto ? $producto->nombre : ''; // Si no se encuentra, deja el nombre vacío

            // Crear el detalle de la composición del plato
            $detalle = new PlatoDetalle();
            $detalle->plato_id = $validated['plato_id']; // Asegúrate de que estés pasando el id del plato
            $detalle->producto_id = $validated['producto_id'];
            $detalle->producto = $productoNombre; // Guardar el nombre del producto
            $detalle->cantidad = $validated['cantidad'];
            $detalle->unidad_medida = $validated['unidad_medida'];
            $detalle->save();

            // Obtener el producto para la respuesta
            $producto = Producto::find($validated['producto_id']);

            return response()->json([
                'success' => true,
                'producto' => $producto
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el producto: ' . $e->getMessage(),
            ], 500);
        }
    }
}
