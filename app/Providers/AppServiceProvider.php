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
        $requerimientos =   Requerimiento::where('estado','<>','ANULADO')->get();

        View::share('empresa', $empresa);
        View::share('requerimientos', $requerimientos);

    }
}
