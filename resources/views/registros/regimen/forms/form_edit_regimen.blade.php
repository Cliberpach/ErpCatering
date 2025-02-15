<form action="" id="formActualizarRegimen" >    
    @csrf
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="nombre" class="required_field mb-2" style="font-weight: bold;">Nombre</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-file-signature"></i>                   
                </span>
                <input value="{{ $regimen->nombre }}" required id="nombre" maxlength="255" name="nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Nombre" aria-describedby="basic-addon1">
             </div>                  
            <span class="nombre_error msgError" style="color:red;"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="descripcion" class="required_field mb-2" style="font-weight: bold;">Descripción</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-keyboard"></i>                  
                </span>
                <textarea required id="descripcion" maxlength="500" name="descripcion" class="form-control" placeholder="Descripción" aria-label="Descripción" aria-describedby="basic-addon1">{{ $regimen->descripcion }}</textarea>
            </div>                  
            <span class="descripcion_error msgError" style="color:red;"></span>
        </div>  
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="dias_trabajo" class="required_field mb-2" style="font-weight: bold;">Días de Trabajo</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-calendar-day"></i>                  
                </span>
                <input value="{{ $regimen->dias_trabajo }}" required id="dias_trabajo" name="dias_trabajo" type="number" min="1" class="form-control" placeholder="Días de Trabajo" aria-label="Días de Trabajo" aria-describedby="basic-addon1">
            </div>                  
            <span class="dias_trabajo_error msgError" style="color:red;"></span>
        </div>  
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="dias_descanso" class="required_field mb-2" style="font-weight: bold;">Días de Descanso</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-calendar-day"></i>                  
                </span>
                <input value="{{ $regimen->dias_descanso }}" required id="dias_descanso" name="dias_descanso" type="number" min="1" class="form-control" placeholder="Días de Descanso" aria-label="Días de Descanso" aria-describedby="basic-addon1">
            </div>                  
            <span class="dias_descanso_error msgError" style="color:red;"></span>
        </div>  
    </div>
</form> 