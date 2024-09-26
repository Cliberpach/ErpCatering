<form action="" method="post" id="formActualizarProveedor">
    @csrf
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label class="required_field" for="tipo_documento" style="font-weight: bold;">TIPO DOCUMENTO</label>
            <select required name="tipo_documento" required class="form-select select2_form" id="tipo_documento" data-placeholder="Seleccionar" onchange="changeTipoDoc()">
                <option></option>
                @foreach ($tipos_documento as $tipo_documento)
                    <option 
                    @if ($tipo_documento->id === $proveedor->tipo_documento_id)
                        selected
                    @endif
                    value="{{$tipo_documento->id}}">{{$tipo_documento->descripcion}}</option>
                @endforeach
            </select>
            <span class="tipo_documento_error msgError"  style="color:red;"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label for="nro_documento" style="font-weight: bold;" class="required_field">Nro Doc</label>
            <div class="input-group mb-3">
                <button 
                @if ($proveedor->tipo_documento_id !== 1 && $proveedor->tipo_documento_id !== 2)
                    disabled
                @endif
                id="btn_consultar_documento" class="btn btn-primary" type="button" id="button-addon1">
                    <i class="fa-solid fa-magnifying-glass" style="color:white;"></i>
                </button>
                <input value="{{$proveedor->nro_documento}}" required readonly id="nro_documento" name="nro_documento" type="text" class="form-control" placeholder="Nro de Documento" aria-label="Example text with button addon" aria-describedby="button-addon1">
            </div>                 
            <span class="nro_documento_error msgError"  style="color:red;"></span>
        </div>  

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label for="nombre" style="font-weight: bold;" class="required_field">NOMBRE</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-file-signature"></i>
                </span>
                <input value="{{$proveedor->nombre}}" required id="nombre" name="nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="nombre_error msgError"  style="color:red;"></span>
        </div>
        
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label for="direccion" style="font-weight: bold;" >DIRECCIÓN</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                <input value="{{$proveedor->direccion}}" id="direccion" name="direccion" type="text" class="form-control" placeholder="Dirección" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="direccion_error msgError"  style="color:red;"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label for="telefono" style="font-weight: bold;" >TELÉFONO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-phone-volume"></i>
                </span>
                <input value="{{$proveedor->telefono}}" id="telefono" name="telefono" type="text" class="form-control" placeholder="Dirección" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="telefono_error msgError"  style="color:red;"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label for="correo" style="font-weight: bold;" >CORREO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-at"></i>
                </span>
                <input value="{{$proveedor->correo}}" id="correo" name="correo" type="email" class="form-control" placeholder="Dirección" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="correo_error msgError"  style="color:red;"></span>
        </div>
    </div>
</form>