<form action="" id="formAsignarPersonal" method="post">    
    <div class="row">
        @csrf   
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pb-2">
            <div class="table-responsive">
                @include('jornales.registro_labor.tables.table_detalle_asistencia')
            </div>
            <span class="personal_error msgError"  style="color:red;"></span>
        </div>     
    </div>
</form> 