<form action="" id="formRegistrarUsuario" method="post">    
    <div class="row">
            @csrf       
            {{-- <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="nombre" class="required_field" style="font-weight: bold;">Nombre</label>
                <input maxlength="255" name="nombre" required type="text" class="form-control" placeholder="Usuario">
                <span class="nombre_error msgError"  style="color:red;"></span>
            </div> --}}
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="colaborador" class="required_field" style="font-weight: bold;">Colaborador</label>
                <select required name="colaborador" required class="form-select" id="colaborador" data-placeholder="Seleccionar">
                    <option></option>
                    @foreach ($colaboradores as $colaborador)
                        <option value="{{$colaborador->id}}">
                            {{$colaborador->nombre.' - '.$colaborador->tipo_documento_nombre.':'.$colaborador->nro_documento}}
                        </option>
                    @endforeach
                    
                </select>
                <span class="colaborador_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="correo" class="required_field" style="font-weight: bold;">Correo</label>
                <input maxlength="255" name="correo" id="correo" required type="email" class="form-control" placeholder="Correo">
                <span class="correo_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="password" class="required_field" style="font-weight: bold;">Contraseña</label>
                <div class="input-group mb-3">
                    <button style="width:50px;" class="btn btn-primary btn_ver_password password_oculto" type="button" id="button-addon1">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                    <input maxlength="50" type="password" id="password" name="password" class="form-control" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
                </div>
                <span class="password_error msgError" style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="repetir_password" class="required_field" style="font-weight: bold;">Repetir contraseña</label>
                <div class="input-group mb-3">
                    <button style="width:50px;"  class="btn btn-primary btn_ver_repetir_password password_oculto" type="button" id="button-addon1">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                    <input maxlength="50" type="password" id="repetir_password" name="repetir_password" class="form-control" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
                </div>
                <span class="repetir_password_error msgError" style="color:red;"></span>
            </div>
    </div>
</form> 