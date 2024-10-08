<form action="" id="formCotizacionToOrden" method="post">    
    <div class="row">
        @csrf      

        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="ec" style="font-weight: bold;">FECHA ENTREGA</label>
                        <input required  value="<?php echo date('Y-m-d'); ?>"  type="date" name="fecha_entrega" id="fecha_entrega" class="form-control">
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="terminos_entrega" class="required_field" style="font-weight: bold;">TÉRMINOS DE ENTREGA</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-handshake"></i>                            
                            </span>
                            <select required name="terminos_entrega" id="terminos_entrega" data-placeholder="Seleccionar" class="select2_form">
                                <option value="PUESTO EN OBRA">PUESTO EN OBRA</option>
                            </select>                        
                        </div>
                    </div>
                    {{-- <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="fecha_entrega" style="font-weight: bold;">FECHA ENTREGA</label>
                        <input value="<?php echo date('Y-m-d'); ?>" required type="date" name="fecha_entrega" id="fecha_entrega" class="form-control">
                    </div>  --}}
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="proveedor" style="font-weight: bold;">PROVEEDOR</label> <i class="fa-solid fa-plus btn btn-primary" onclick="openMdlNuevoProveedor();"></i>
                        <select required name="proveedor" id="proveedor" data-placeholder="Seleccionar" class="select2_form">
                            @foreach ($proveedores as $proveedor)
                                <option value="{{$proveedor->id}}">{{$proveedor->tipo_documento_descripcion.':'.$proveedor->nro_documento.'-'.$proveedor->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="direccion" class="required_field" style="font-weight: bold;">DIRECCIÓN DE OBRA</label>
                        <div class="form-floating">
                            <textarea required maxlength="200" id="direccion" name="direccion" class="form-control" placeholder="Leave a comment here" id="floatingTextarea">{{$requerimiento->direccion}}</textarea>
                            <label for="floatingTextarea">Dirección</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="modalidad_pago" style="font-weight: bold;">MODALIDAD DE PAGO</label>
                        <select required name="modalidad_pago" id="modalidad_pago" data-placeholder="Seleccionar" class="select2_form">
                            @foreach ($modalidades_pago as $modalidad_pago)
                                @if ($modalidad_pago->tipo == "CREDITO")
                                    <option value="{{$modalidad_pago->id}}">
                                        {{$modalidad_pago->tipo.'-'.$modalidad_pago->nro_dias.''.'DÍAS'}}
                                    </option>    
                                @endif
                                @if ($modalidad_pago->tipo == "CONTADO")
                                    <option value="{{$modalidad_pago->id}}">
                                        {{$modalidad_pago->tipo}}
                                    </option>    
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="proyecto" class="required_field" style="font-weight: bold;">PROYECTO</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-diagram-project"></i>                            
                            </span>
                            <input value="{{$requerimiento->nombre}}" required readonly  id="proyecto" name="proyecto" type="text" class="form-control inputDecimalPositivoLibre" placeholder="PROYECTO" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="proveedor" style="font-weight: bold;">DOCUMENTO</label>
                        <select required name="tipo_doc" id="tipo_doc" data-placeholder="Seleccionar" class="select2_form">
                            <option value="FACTURA">FACTURA</option>
                            <option value="BOLETA">BOLETA</option>
                        </select>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="persona_contacto" class="required_field" style="font-weight: bold;">PERSONA DE CONTACTO</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-address-book"></i>                           
                            </span>
                            <select required name="persona_contacto" id="persona_contacto" data-placeholder="Seleccionar" class="select2_form">
                                <option value="{{$requerimiento->supervisor_id}}">{{$requerimiento->persona_contacto.' - '.'(S)'}}</option>
                                @foreach ($proyecto_personal as $personal)
                                    <option value="{{$personal->colaborador_id}}">{{$personal->persona_contacto}}</option>
                                @endforeach
                            </select>                        
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="observacion" style="font-weight: bold;">OBSERVACIÓN</label>
                        <div class="form-floating">
                            <textarea name="observacion" id="observacion" class="form-control" placeholder="Leave a comment here" maxlength="200"></textarea>
                            <label for="floatingTextarea">Observación</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-3 mb-3">
                <div class="card">
                    <div class="card-header" style="background-color: rgb(0, 102, 255);font-weight:bold;color:white;">
                    SELECCIONAR PRODUCTOS
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-lg-5 col-md-7 col-sm-12 col-xs-12">
                                <label for="categoria" style="font-weight: bold;">PRODUCTO</label>

                                <div class="input-group mb-3">
                                    <input id="producto" name="producto" readonly type="text" class="form-control" placeholder="Producto" aria-label="Recipient's username" aria-describedby="button-addon2">
                                    <button class="btn btn-primary" type="button" id="button-addon2" onclick="openMdlProductos()">
                                        <i class="fa-solid fa-magnifying-glass"></i> Buscar
                                    </button>
                                  </div>
                            </div>

                            <div class="col-lg-3 col-md-5 col-sm-12 col-xs-12">
                                <label for="categoria" style="font-weight: bold;">UNIDAD</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </span>
                                    <input id="unidad" name="unidad" readonly type="text" class="form-control" placeholder="Unidad" aria-label="Username" aria-describedby="basic-addon1">
                                  </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                <label for="categoria" style="font-weight: bold;">CANTIDAD</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fa-solid fa-box-open"></i>                                    
                                    </span>
                                    <input id="cantidad" name="cantidad" type="text" class="form-control inputEnteroPositivo" placeholder="Cantidad" aria-label="Username" aria-describedby="basic-addon1">
                                  </div>
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-3 d-flex justify-content-end">
                                <button class="btn btn-primary btnAgregarProducto" type="button">
                                    <i class="fa-solid fa-cart-plus"></i> AGREGAR 
                                </button>
                            </div>
                        </div>

                        {{-- <div class="row mt-3">
                            <div class="col-12">
                               @include('logistica.registro_compra.tables.table_productos')
                            </div>
                        </div> --}}
                       
                    </div>
                </div>
            </div>
        </div>  
        
        <div class="row mt-3">
            <div class="col-12 mt-3 mb-3">
                <div class="card">
                    <div class="card-header" style="background-color: rgb(0, 102, 255);font-weight:bold;color:white;">
                    DETALLE DE LA COMPRA
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    @include('compras.cotizacion_compra.tables.table_compra_detalle')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

           
    </div>
</form> 