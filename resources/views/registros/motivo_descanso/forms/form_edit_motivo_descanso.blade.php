<form action="" id="formActualizarMotivo" method="post">    
    @csrf
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="descripcion" class="required_field mb-2" style="font-weight: bold;">Descripción</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-keyboard"></i>                  
                </span>
                <textarea required id="descripcion" maxlength="500" name="descripcion" class="form-control" placeholder="Descripción" aria-label="Descripción" aria-describedby="basic-addon1">{{ $motivo->descripcion }}</textarea>
            </div>                  
            <span class="descripcion_error msgError" style="color:red;"></span>
        </div>  
    </div>
</form> 