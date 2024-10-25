<form action="" method="post" id="formOrdenCompraToOrdenPago">
    @csrf
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="fecha_registro" class="required_field" style="font-weight: bold;">FECHA REGISTRO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons/date/date2.png')}}" alt="coins"/>
                </span>
                <input value="{{ date('Y-m-d') }}" readonly required id="fecha_registro" name="fecha_registro" type="date" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="fecha_registro_error msgError"  style="color:red;"></span>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="colaborador_registrador" class="required_field" style="font-weight: bold;">COLABORADOR REGISTRADOR</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons/user/user1.png')}}" alt="coins"/>
                </span>
                <input value="{{$colaborador_registrador->nombre}}" readonly required id="colaborador_registrador" name="colaborador_registrador" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="proveedor" class="required_field" style="font-weight: bold;">PROYECTO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons_animados/proyecto/proyecto1.gif')}}" alt="coins"/>
                </span>
                <input value="{{ $proyecto->nombre }}" readonly required id="proveedor" name="proveedor" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
       
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="documento" class="required_field" style="font-weight: bold;">DOCUMENTO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons/invoice/invoice1.png')}}" alt="coins"/>
                </span>
                <input value="{{ $orden_compra->documento }}" readonly required id="documento" name="documento" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="moneda" class="required_field" style="font-weight: bold;">MONEDA</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons/money/money1.png')}}" alt="coins"/>
                </span>
                <input value="{{$orden_compra->moneda}}" readonly required id="moneda" name="moneda" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="medio_pago" class="required_field" style="font-weight: bold;">MEDIO DE PAGO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons_animados/efectivo/efectivo1.gif')}}" alt="coins"/>
                </span>
                <input value="TRANSFERENCIA" readonly required id="medio_pago" name="medio_pago" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span class="medio_pago_error msgError"  style="color:red;"></span>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-3">
            <label for="observacion" style="font-weight: bold;">OBSERVACIÓN</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons/texto/texto1.gif')}}" alt="coins"/>
                </span>
                <div class="form-floating">
                    <textarea maxlength="200" name="observacion" style="background-color: rgb(253, 253, 215);" class="form-control" placeholder="Leave a comment here" id="observacion"></textarea>
                    <label for="floatingTextarea">OBSERVACIÓN</label>
                </div>                </div>
            <span class="observacion_error msgError"  style="color:red;"></span>
        </div> 

        <hr>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="proveedor" class="required_field" style="font-weight: bold;">PROVEEDOR</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons/proveedor/proveedor1.png')}}" alt="coins"/>
                </span>
                <input value="{{ $orden_compra->proveedor_tipo_documento.':'.$orden_compra->proveedor_nro_documento.'-'.$orden_compra->proveedor_nombre }}" readonly required id="proveedor" name="proveedor" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="banco" class="required_field" style="font-weight: bold;">BANCO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons/banco/banco1.png')}}" alt="coins"/>
                </span>
                <input value="{{$orden_compra->banco_nombre}}" readonly required id="banco" name="banco" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="nro_cuenta" class="required_field" style="font-weight: bold;">N° CUENTA</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons/card/card1.png')}}" alt="coins"/>
                </span>
                <input value="{{$orden_compra->proveedor_nro_cuenta}}" readonly required id="nro_cuenta" name="nro_cuenta" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="cci" class="required_field" style="font-weight: bold;">CCI</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons/card/card1.png')}}" alt="coins"/>
                </span>
                <input value="{{$orden_compra->proveedor_cci}}" readonly required id="cci" name="cci" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="cci" class="required_field" style="font-weight: bold;">CUENTA DETRACCIÓN</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <img width="30" height="30" src="{{asset('img/icons/card/card1.png')}}" alt="coins"/>
                </span>
                <input value="{{$orden_compra->proveedor_nro_cuenta_detraccion}}" readonly required id="cci" name="cci" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3">

            <label for="inputFileImgPago" style="font-weight: bold;" >SUBIR IMÁGENES DEL PAGO</label>
            <button type="button" id="btnAgregarImagenPago" class="btn btn-primary" style="margin-left:6px;">
                <i class="fa-solid fa-plus"></i> AGREGAR
            </button>
            
            <input class="form-control form-control-sm" style="margin-top:6px;" id="inputFileImgPago" type="file" accept=".jpg, .jpeg, .png">
            <span style="color:rgb(46, 87, 175); font-style: italic;">FORMATO JPG, JPEG, PNG</span>

        </div>
        <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12">
           <div class="table-responsive">
                @include('finanzas.lista_ordenes_compra.tables.table_imagenes_pago')
           </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-12">
            <label for="" style="font-weight: bold;">DETALLE DE LA ORDEN DE PAGO</label>
            @include('finanzas.lista_ordenes_compra.tables.table_orden_pago_detalle')
        </div>
    </div>

    <div class="row justify-content-end">
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">  
            @include('finanzas.lista_ordenes_compra.tables.table_orden_compra_to_orden_pago_montos')
        </div>
    </div>

</form>