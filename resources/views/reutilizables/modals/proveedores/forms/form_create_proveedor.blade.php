<form action="" method="post" id="formRegistrarProveedor">
    @csrf
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label class="required_field" for="tipo_documento" style="font-weight: bold;">TIPO DOCUMENTO</label>
            <select required name="tipo_documento" required class="form-select select2_form" id="tipo_documento" data-placeholder="Seleccionar" onchange="changeTipoDoc()">
                <option></option>
                @foreach ($tipos_documento as $tipo_documento)
                    <option value="{{$tipo_documento->id}}">{{$tipo_documento->descripcion}}</option>
                @endforeach
            </select>
            <span class="tipo_documento_error_proveedor msgErrorProveedor"  style="color:red;"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label for="nro_documento" style="font-weight: bold;" class="required_field">Nro Doc</label>
            <div class="input-group mb-3">
                <button id="btn_consultar_documento" disabled class="btn btn-primary" type="button" id="button-addon1">
                    <i class="fa-solid fa-magnifying-glass" style="color:white;"></i>
                </button>
                <input required readonly id="nro_documento" name="nro_documento" type="text" class="form-control" placeholder="Nro de Documento" aria-label="Example text with button addon" aria-describedby="button-addon1">
            </div>                 
            <span class="nro_documento_error_proveedor msgErrorProveedor"  style="color:red;"></span>
        </div>  

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label for="nombre" style="font-weight: bold;" class="required_field">NOMBRE</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-file-signature"></i>
                </span>
                <input required id="nombre" name="nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="nombre_error_proveedor msgErrorProveedor"  style="color:red;"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
            <label for="banco" style="font-weight: bold;">BANCO</label>
            <select name="banco" required class="form-select select2_form" id="banco" data-placeholder="Seleccionar">
                <option></option>
                @foreach ($bancos as $banco)
                    <option value="{{$banco->id}}">{{$banco->nombre}}</option>
                @endforeach
            </select>
            <span class="banco_error_proveedor msgError"  style="color:red;"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
            <label for="nro_cuenta" style="font-weight: bold;">N° CUENTA</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-credit-card"></i>
                </span>
                <input maxlength="40" id="nro_cuenta" name="nro_cuenta" type="text" class="form-control inputEnteroPositivo" placeholder="N° CUENTA" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span style="color:blue; font-style:italic;display:block;">SOLO NÚMEROS (9999999900302) ENTRE 10 - 40 DÍGITOS</span>
            <span class="nro_cuenta_error_proveedor msgError"  style="color:red;"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
            <label for="cci" style="font-weight: bold;">CCI</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-credit-card"></i>
                </span>
                <input maxlength="40" id="cci" name="cci" type="text" class="form-control inputEnteroPositivo" placeholder="CCI" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span style="color:blue; font-style:italic;display:block;">SOLO NÚMEROS (9999999900302) ENTRE 10 - 40 DÍGITOS</span>
            <span class="cci_error_proveedor msgError"  style="color:red;"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
            <label for="cuenta_detraccion" style="font-weight: bold;" >N° CUENTA DETRACCIÓN</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-credit-card"></i>
                </span>
                <input maxlength="40" id="cuenta_detraccion" name="cuenta_detraccion" type="text" class="form-control inputEnteroPositivo" placeholder="N° CUENTA DETRACCIÓN" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span style="color:blue; font-style:italic;display:block;">SOLO NÚMEROS (9999999900302) ENTRE 10 - 40 DÍGITOS</span>
            <span class="cuenta_detraccion_error_proveedor msgError"  style="color:red;"></span>
        </div>
        
        
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label for="direccion" style="font-weight: bold;" >DIRECCIÓN</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                <input id="direccion" name="direccion" type="text" class="form-control" placeholder="Dirección" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="direccion_error_proveedor msgErrorProveedor"  style="color:red;"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label for="telefono" style="font-weight: bold;" >TELÉFONO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-phone-volume"></i>
                </span>
                <input id="telefono" name="telefono" type="text" class="form-control" placeholder="Dirección" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="telefono_error_proveedor msgErrorProveedor"  style="color:red;"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
            <label for="correo" style="font-weight: bold;" >CORREO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-at"></i>
                </span>
                <input id="correo" name="correo" type="email" class="form-control" placeholder="Dirección" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="correo_error_proveedor msgErrorProveedor"  style="color:red;"></span>
        </div>
    </div>
</form>