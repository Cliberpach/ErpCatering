<form action="" id="formRegistrarMaquinaria" method="post">    
    <div class="row">
        @csrf      
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="nombre" class="required_field mb-2" style="font-weight: bold;">Nombre</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-file-signature"></i>                   
                </span>
                <input required id="nombre" maxlength="260"  name="nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1">
             </div>                  
            <span class="nombre_error msgError"  style="color:red;"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label class="required_field mb-2" for="tipo_gasto" style="font-weight: bold;">TIPO DE GASTO</label>
            <i class="fa-solid fa-plus btn btn-primary" style="border-radius: 90%;padding-right:8px;padding-left:8px;" onclick="openMdlNuevaTablaGeneralDetalle();"></i>
            <select required name="tipo_gasto" required class="form-select select2_form" id="tipo_gasto" data-placeholder="Seleccionar">
                <option></option>
                @foreach ($tipos_gasto as $tipo_gasto)
                    <option
                    value="{{$tipo_gasto->id}}">{{$tipo_gasto->descripcion}}</option>
                @endforeach
            </select>
            <span class="tipo_gasto_error msgError"  style="color:red;"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="costo_gasto" class="required_field mb-2" style="font-weight: bold;">COSTO GASTO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-money-check-dollar"></i>                  
                </span>
                <input value="1.00" required id="costo_gasto" maxlength="20"  name="costo_gasto" type="text" class="form-control numero-input" placeholder="Costo gasto" aria-label="Username" aria-describedby="basic-addon1">
            </div>                  
            <span class="costo_gasto_error msgError"  style="color:red;"></span>
        </div>  
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="costo_gasto" class="mb-2" style="font-weight: bold;">OBSERVACIÓN</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-keyboard"></i>                  
                </span>
                <div class="form-floating">
                    <textarea name="observacion" class="form-control" placeholder="Leave a comment here" id="floatingTextarea"></textarea>
                    <label for="floatingTextarea">Observación</label>
                </div>
            </div>                  
            <span class="costo_gasto_error msgError"  style="color:red;"></span>
        </div>  
       
    </div>
</form>