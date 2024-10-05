<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
           //======= REGISTRO DE PERMISOS =======
           $items = [
            'panel_control.dashboard',
            'registros.colaborador',
            'registros.cargo',
            'registros.maquinaria',
            'registros.proyecto',
            'registros.almacen',
            'registros.categoria',
            'registros.marca',
            'registros.producto',
            'jornal.registro_labor',
            //'jornal.consulta_labor',
            'trabajo_equipo.registro_tarea',
            //'trabajo_equipo.consulta_tarea',
            'requerimientos.requerimientos',
            'logistica.registro_salida',
            'logistica.lista_requerimientos',
            'compras.cotizacion_compra',
            'compras.registro_compra',
            'compras.proveedor',
            'plan_proyecto.tarea',
            'herramientas.usuarios',
            'herramientas.roles',
            'herramientas.tabla_general',
            'herramientas.empresa',
            'consultas.personal',
            'consultas.maquinaria',
            'consultas.producto'
        ];
        
        foreach ($items as $item) {
            $permiso = new Permission();
            $permiso->name = $item;
            $permiso->save();
        }

    }
}
