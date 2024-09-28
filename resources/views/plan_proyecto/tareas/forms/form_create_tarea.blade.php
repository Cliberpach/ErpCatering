<form action="" method="post">
    <div class="row mb-3">
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 mb-3">
            <label for="tarea_nombre" class="required_field" style="font-weight: bold;">NOMBRE</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-person-digging"></i></span>
                <input name="tarea_nombre" id="tarea_nombre" type="text" class="form-control" placeholder="Nombre Tarea" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-3">
            <label for="tarea_fecha_inicio" class="required_field" style="font-weight: bold;">FECHA INICIO</label>
            <input type="date" name="tarea_fecha_inicio" id="tarea_fecha_inicio" class="form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-3">
            <label for="tarea_fecha_fin" class="required_field" style="font-weight: bold;">FECHA FIN</label>
            <input type="date" name="tarea_fecha_fin" id="tarea_fecha_fin" class="form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <label for="tarea_observacion" class="required_field" style="font-weight: bold;">OBSERVACIÓN</label>
            <div class="form-floating">
                <input id="tarea_observacion" name="tarea_observacion" type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                <label for="floatingInput">OBSERVACIÓN</label>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background-color: rgb(0, 102, 255);font-weight:bold;color:white;">
                    <div class="col-12" style="display: flex;justify-content:space-between;align-items:center;">
                        <p style="margin:0;">SUBTAREAS</p>
                        <button class="btn btn-warning" type="button"> NUEVO <i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @include('plan_proyecto.tareas.tables.table_list_subtareas')
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>