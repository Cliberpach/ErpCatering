<form action="" method="post" id="formActualizarFeriado">

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <label for="fecha" class="required_field" style="font-weight: bold;">FECHA</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-calendar-check"></i>
                </span>
                <input value="{{$feriado->fecha}}"  required id="fecha" name="fecha" type="date" class="form-control"  aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="fecha_error msgError"  style="color:red;"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <label for="descripcion" class="required_field" style="font-weight: bold;">DESCRIPCIÓN</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-hiking"></i>
                </span>
                <input value="{{$feriado->descripcion}}"  maxlength="200" required id="descripcion" name="descripcion" type="text" class="form-control" placeholder="Nombre del feriado" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="descripcion_error msgError"  style="color:red;"></span>
        </div>
    </div>
</form>