<?php

namespace App\Providers;

use App\Models\Requerimientos\Requerimiento;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $empresa        =   DB::table('empresas')->where('id', 1)->first();
        $requerimientos =   DB::select('select 
                            r.id,
                            co.nombre as supervisor_nombre,
                            pr.nombre as proyecto_nombre,
                            r.created_at as fecha_registro,
                            r.estado
                            from 
                            requerimientos as r
                            inner join proyectos as pr on pr.id = r.proyecto_id
                            inner join colaboradores as co on co.id = r.supervisor_id
                            where r.estado != "ANULADO"
                            order by r.id desc');

        View::share('empresa', $empresa);
        View::share('requerimientos', $requerimientos);

    }
}
