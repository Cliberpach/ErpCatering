<form action="" id="formActualizarEmpresa" method="post">
    @csrf
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="ruc" class="required_field" style="font-weight: bold;">RUC</label>
                    <div class="input-group">
                        <input required value="{{$empresa->ruc}}" id="ruc" name="ruc" maxlength="11" type="text" class="form-control" placeholder="RUC" aria-label="Recipient's username" aria-describedby="button-addon2">
                        <button class="btn btn-primary" type="button" id="btn_consultar_documento">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="razon_social" class="required_field" style="font-weight: bold;">RAZÓN SOCIAL</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-regular fa-building"></i></span>
                        <input required maxlength="150" value="{{$empresa->razon_social}}" name="razon_social" id="razon_social" type="text" class="form-control" placeholder="RAZÓN SOCIAL" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="direccion"  style="font-weight: bold;">DIRECCIÓN</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-map-location-dot"></i></span>
                        <input maxlength="150" value="{{$empresa->direccion}}" name="direccion" id="direccion" type="text" class="form-control" placeholder="DIRECCIÓN" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="telefono" style="font-weight: bold;">TELÉFONO</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-phone-volume"></i></span>
                        <input maxlength="20" value="{{$empresa->telefono}}" name="telefono" id="telefono" type="text" class="form-control" placeholder="TELÉFONO" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="correo" style="font-weight: bold;">CORREO</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-at"></i></span>
                        <input maxlength="100" value="{{$empresa->correo}}" name="correo" id="correo" type="email" class="form-control" placeholder="CORREO" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3 d-flex justify-content-center">
            <div class="row">
                <div class="col-12">
                    <div>
                        <label for="img_empresa" style="font-weight:bold;" class="form-label">IMAGEN</label> <i class="fa-solid fa-trash-can btn btn-danger btnSetImageDefault"></i>
                        <input id="img_empresa" name="img_empresa" class="form-control form-control-sm"  type="file" accept="image/*">
                    </div> 
                </div>
                <div class="col-12">
                    <div id="img_preview_container" style="overflow-x:hidden;overflow-y:hidden;heigth:310px;width:100%;border: 2px dashed #ddd; border-radius: 10px; padding: 10px; text-align: center;display:flex;align-items:center;justify-content:center;">
                        <img class="imgShowLightBox" src="{{asset($empresa->img_ruta)}}" id="img_vista_previa" style="height: 260px;max-width:260px; object-fit: cover;cursor:pointer;">
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>