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
                    <span class="ruc_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="razon_social" class="required_field" style="font-weight: bold;">RAZÓN SOCIAL</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('img/icons/empresa/empresa2.png')}}" alt="coins"/>
                        </span>
                        <input required maxlength="150" value="{{$empresa->razon_social}}" name="razon_social" id="razon_social" type="text" class="form-control" placeholder="RAZÓN SOCIAL" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="razon_social_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="direccion"  style="font-weight: bold;">DIRECCIÓN</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('img/icons/ubicacion/ubicacion2.png')}}" alt="coins"/>
                        </span>
                        <input maxlength="150" value="{{$empresa->direccion}}" name="direccion" id="direccion" type="text" class="form-control" placeholder="DIRECCIÓN" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="direccion_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="telefono" style="font-weight: bold;">TELÉFONO</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('img/icons/telefono/telefono2.png')}}" alt="coins"/>
                        </span>
                        <input maxlength="20" value="{{$empresa->telefono}}" name="telefono" id="telefono" type="text" class="form-control" placeholder="TELÉFONO" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="telefono_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="correo" style="font-weight: bold;">CORREO</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('img/icons/email/email1.png')}}" alt="coins"/>
                        </span>
                        <input maxlength="100" value="{{$empresa->correo}}" name="correo" id="correo" type="email" class="form-control" placeholder="CORREO" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="correo_error msgError"  style="color:red;"></span>
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
                        <img class="imgShowLightBox"
                        @if ($empresa->img_ruta)
                            src="{{asset($empresa->img_ruta)}}"
                        @else 
                            src="{{asset('img/img_default.png')}}"
                        @endif 
                        id="img_vista_previa" style="height: 260px;max-width:260px; object-fit: cover;cursor:pointer;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mt-3 mb-3">
            <div class="card">
                <div class="card-header" style="background-color: rgb(0, 102, 255);font-weight:bold;color:white;">
                DATOS DE GUÍA DE REMISIÓN
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="usuario_sol" class="" style="font-weight: bold;">USUARIO SOL</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/user/user1.png')}}" alt="coins"/>
                                </span>
                                <input value="{{$empresa->usuario_sol}}" maxlength="100" type="text" name="usuario_sol" id="usuario_sol" class="form-control">
                            </div>
                            <span class="usuario_sol_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="clave_sol" class="" style="font-weight: bold;">CLAVE SOL</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/password/password1.png')}}" alt="coins"/>
                                </span>
                                <input value="{{$empresa->clave_sol}}" value="" maxlength="100" type="text" name="clave_sol" id="clave_sol" class="form-control">
                            </div>
                            <span class="clave_sol_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="usuario_api_guias" class="" style="font-weight: bold;">USUARIO API</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/api/api1.png')}}" alt="coins"/>
                                </span>
                                <input value="{{$empresa->usuario_api_guias}}" value="" maxlength="100" type="text" name="usuario_api_guias" id="usuario_api_guias" class="form-control">
                            </div>
                            <span class="usuario_api_guias_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="clave_api_guias" class="" style="font-weight: bold;">CLAVE API</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/password/password1.png')}}" alt="coins"/>
                                </span>
                                <input value="{{$empresa->clave_api_guias}}" maxlength="100" type="text" name="clave_api_guias" id="clave_api_guias" class="form-control">
                            </div>
                            <span class="clave_api_guias_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <div class="">
                                <label for="formFileSm" class="form-label" style="font-weight: bold;">CERTIFICADO PEM</label> <i class="fa-solid fa-trash-can btn btn-danger btnDeleteCertificado"></i>
                                <input accept=".pem" class="form-control form-control-sm" id="certificado" name="certificado" type="file">

                                @if (!$empresa->certificado_ruta)
                                    <p style="margin:0; color:blue; font-style:italic;" class="certificado_previo">SIN CERTIFICADO</p>
                                @else 
                                    <p style="margin:0; color:blue; font-style:italic;" class="certificado_previo">
                                        {{$empresa->certificado_nombre}}
                                    </p>
                                @endif
                            </div>
                            <span class="certificado_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="nro_inicio" class="required_field" style="font-weight: bold;">N° INICIO</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/hash/hash2.png')}}" alt="coins"/>
                                </span>
                                <input value="{{$empresa->nro_inicio}}" value="" maxlength="100" type="text" name="nro_inicio" id="nro_inicio" class="form-control inputEnteroPositivo">
                            </div>
                            <span class="nro_inicio_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="serie" style="font-weight: bold;">SERIE</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/hash/hash2.png')}}" alt="coins"/>
                                </span>
                                <input disabled value="{{$empresa->serie}}" value="" maxlength="100" type="text" name="serie" id="serie" class="form-control">
                            </div>
                            <span class="serie_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="estado" style="font-weight: bold;">ESTADO</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/estado/estado1.png')}}" alt="coins"/>
                                </span>
                                <input disabled value="{{ $empresa->iniciado == 1 ? 'INICIADO' : 'SIN INICIAR' }}" maxlength="100" type="text" name="estado" id="estado" class="form-control">
                            </div>
                            <span class="estado_error msgError"  style="color:red;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>