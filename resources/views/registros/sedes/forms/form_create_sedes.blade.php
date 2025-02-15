<form action="" id="formCreateSedes" method="post">    
    <div class="row">
        @csrf      
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="nombre" class="required_field mb-2" style="font-weight: bold;">Nombre</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-file-signature"></i>                   
                </span>
                <input required id="nombre" maxlength="255" name="nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Nombre" aria-describedby="basic-addon1">
             </div>                  
            <span class="nombre_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="direccion" class="required_field mb-2" style="font-weight: bold;">Dirección</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-location-dot"></i>                  
                </span>
                <input required id="direccion" maxlength="500" name="direccion" type="text" class="form-control" placeholder="Dirección" aria-label="Dirección" aria-describedby="basic-addon1">
            </div>                  
            <span class="direccion_error msgError" style="color:red;"></span>
        </div>  

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="encargado" class="required_field mb-2" style="font-weight: bold;">Encargado</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-user-tie"></i>                  
                </span>
                <input required id="encargado" maxlength="255" name="encargado" type="text" class="form-control" placeholder="Encargado" aria-label="Encargado" aria-describedby="basic-addon1">
            </div>                  
            <span class="encargado_error msgError" style="color:red;"></span>
        </div>  
    </div>
</form>
