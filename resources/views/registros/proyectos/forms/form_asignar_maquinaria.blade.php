<form action="" id="formAsignarMaquinaria" method="post">    
    <div class="row">
        @csrf   
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pb-2">
            <div class="table-responsive">
                @include('registros.proyectos.tables.table_maquinarias_libres')
            </div>
            <span class="personal_error msgError"  style="color:red;"></span>
        </div>     
    </div>
</form> 