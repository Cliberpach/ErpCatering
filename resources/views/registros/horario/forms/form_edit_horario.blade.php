<form action="" id="formActualizarHorario" method="post">    
    @csrf
    @method('PUT')

    <div class="row">
        <!-- Nombre del Proyecto -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label for="nombre_proyecto" class="required_field mb-2" style="font-weight: bold;">Nombre del Proyecto</label>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa-solid fa-file-signature"></i></span>
                <input value="{{ $horario->nombre_proyecto }}" required id="nombre_proyecto" maxlength="255" name="nombre_proyecto" type="text" class="form-control" placeholder="Nombre del Proyecto">
            </div>                  
            <span class="nombre_proyecto_error msgError text-danger"></span>
        </div>

        <!-- Descripción -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label for="descripcion" class="mb-2" style="font-weight: bold;">Descripción</label>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa-solid fa-keyboard"></i></span>
                <textarea id="descripcion" maxlength="500" name="descripcion" class="form-control" placeholder="Descripción">{{ $horario->descripcion }}</textarea>
            </div>                  
            <span class="descripcion_error msgError text-danger"></span>
        </div>

        <!-- Hora de Inicio -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label for="hora_inicio" class="required_field mb-2" style="font-weight: bold;">Hora de Inicio</label>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa-solid fa-clock"></i></span>
                <input value="{{ $horario->hora_inicio }}" required id="hora_inicio" name="hora_inicio" type="time" class="form-control">
            </div>                  
            <span class="hora_inicio_error msgError text-danger"></span>
        </div>

        <!-- Hora Final -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label for="hora_final" class="required_field mb-2" style="font-weight: bold;">Hora Final</label>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa-solid fa-clock"></i></span>
                <input value="{{ $horario->hora_final }}" required id="hora_final" name="hora_final" type="time" class="form-control">
            </div>                  
            <span class="hora_final_error msgError text-danger"></span>
        </div>

        <!-- Minutos de Tolerancia -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label for="minutos_tolerancia" class="required_field mb-2" style="font-weight: bold;">Minutos de Tolerancia</label>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa-solid fa-stopwatch"></i></span>
                <input value="{{ $horario->minutos_tolerancia }}" required id="minutos_tolerancia" name="minutos_tolerancia" type="number" min="0" class="form-control" placeholder="Minutos de Tolerancia">
            </div>                  
            <span class="minutos_tolerancia_error msgError text-danger"></span>
        </div>

        <!-- Estado -->

        <!-- Botón de Guardar -->
        <div class="col-12 text-center pt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Guardar Cambios
            </button>
        </div>
    </div>
</form>
