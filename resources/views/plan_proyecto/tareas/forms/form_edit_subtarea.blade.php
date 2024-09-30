<form action="" method="post" id="formEditSubtarea">
    <div class="row mb-3">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="subtarea_nombre_edit" class="required_field" style="font-weight: bold;">NOMBRE</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-person-digging"></i></span>
                <input required maxlength="150" name="subtarea_nombre_edit" id="subtarea_nombre_edit" type="text" class="form-control" placeholder="Nombre Subtarea" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <p class="subtarea_nombre_edit_error msgErrorSubtareaEdit" style="font-weight: bold;color:rgb(207, 15, 15);"></p>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-3">
            <label for="subtarea_fecha_inicio_edit" class="required_field" style="font-weight: bold;">FECHA INICIO</label>
            <input required type="date" name="subtarea_fecha_inicio_edit" id="subtarea_fecha_inicio_edit" class="form-control">
            <p class="subtarea_fecha_inicio_edit_error msgErrorSubtareaEdit" style="font-weight: bold;color:rgb(207, 15, 15);"></p>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-3">
            <label for="subtarea_fecha_fin_edit" class="required_field" style="font-weight: bold;">FECHA FIN</label>
            <input required type="date" name="subtarea_fecha_fin_edit" id="subtarea_fecha_fin_edit" class="form-control">
            <p class="subtarea_fecha_fin_edit_error msgErrorSubtareaEdit" style="font-weight: bold;color:rgb(207, 15, 15);"></p>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <label for="subtarea_observacion_edit" style="font-weight: bold;">OBSERVACIÓN</label>
            <div class="form-floating">
                <input maxlength="300" id="subtarea_observacion_edit" name="subtarea_observacion_edit" type="text" class="form-control" id="floatingInput" placeholder="name@example.com">
                <label for="floatingInput">OBSERVACIÓN</label>
            </div>
            <p class="subtarea_observacion_edit_error msgErrorSubtareaEdit" style="font-weight: bold;color:rgb(207, 15, 15);"></p>
        </div>
    </div>
</form>