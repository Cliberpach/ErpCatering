<form action="" id="formRegistrarColaborador" method="post">    
    <div class="row">
            @csrf   
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required_field" for="tipo_documento" style="font-weight: bold;">TIPO DOCUMENTO</label>
                <select required name="tipo_documento" class="form-select select2_form" id="tipo_documento" data-placeholder="DNI" onchange="changeTipoDoc()">
                    <option></option>
                    @foreach ($tipos_documento as $tipo_documento)
                        <option value="{{$tipo_documento->id}}">{{$tipo_documento->descripcion}}</option>
                    @endforeach
                </select>
                <span class="tipo_documento_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="nro_documento" style="font-weight: bold;" class="required_field">Nro Doc</label>
                <div class="input-group mb-3">
                    <button id="btn_consultar_documento" disabled class="btn btn-primary" type="button">
                        <i class="fa-solid fa-magnifying-glass" style="color:white;"></i>
                    </button>
                    <input required readonly id="nro_documento" name="nro_documento" type="text" class="form-control numero-input" placeholder="Nro de Documento">
                </div>                 
                <span class="nro_documento_error msgError"  style="color:red;"></span>
            </div>    
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="nombre" style="font-weight: bold;" class="required_field">Nombre</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fa-solid fa-user"></i>                    
                    </span>
                    <input required id="nombre" maxlength="260" name="nombre" type="text" class="form-control" placeholder="Nombre">
                </div>                  
                <span class="nombre_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required_field" for="cargo" style="font-weight: bold;">CARGO</label>
                <select required name="cargo" class="form-select select2_form" id="cargo" data-placeholder="Seleccionar">
                    <option></option>
                    @foreach ($cargos as $cargo)
                        <option value="{{$cargo->id}}">{{$cargo->descripcion}}</option>
                    @endforeach
                </select>
                <span class="cargo_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label  for="direccion" style="font-weight: bold;">Dirección</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fa-solid fa-address-book"></i>                    
                    </span>
                    <input maxlength="200" id="direccion" name="direccion" type="text" class="form-control" placeholder="Dirección">
                </div>                   
                <span class="direccion_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="telefono" style="font-weight: bold;">Teléfono</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fa-solid fa-mobile-screen"></i>
                    </span>
                    <input maxlength="20" id="telefono" name="telefono" type="text" class="form-control numero-input" placeholder="Teléfono">
                </div>                 
                <span class="telefono_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required_field" for="horas_semana" style="font-weight: bold;">Horas Semana</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fa-solid fa-clock"></i>
                    </span>
                    <input required maxlength="20" id="horas_semana" name="horas_semana" type="text" class="form-control numero-input" placeholder="Horas semana">
                </div>                
                <span class="horas_semana_error msgError" style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required_field" for="pago_semana" style="font-weight: bold;">Pago Semana</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fa-solid fa-money-bill-1-wave"></i>
                    </span>
                    <input required maxlength="10" name="pago_semana" id="pago_semana" type="text" class="form-control numero-input" placeholder="Pago semana">
                </div>       
                <span class="pago_semana_error msgError" style="color:red;"></span>
            </div>
    </div>
</form>

