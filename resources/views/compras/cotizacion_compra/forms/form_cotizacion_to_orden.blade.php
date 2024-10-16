<form action="" id="formCotizacionToOrden" method="post">    
    <div class="row">
        @csrf      

        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="requerimiento" style="font-weight: bold;">REQUERIMIENTO</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-bell-concierge"></i>                            
                            </span>
                            <input 
                            @if ($requerimiento)
                                value="{{"RE-".$requerimiento->id}}"
                            @else
                                value="SIN REQUERIMIENTO"
                            @endif required disabled  id="requerimiento" name="requerimiento" type="text" class="form-control" placeholder="PROYECTO" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                        <span class="fecha_entrega_error msgError"  style="color:red;"></span>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="fecha_entrega" style="font-weight: bold;">FECHA ENTREGA</label>
                        <input required  value="<?php echo date('Y-m-d'); ?>"  type="date" name="fecha_entrega" id="fecha_entrega" class="form-control">
                        <span class="fecha_entrega_error msgError"  style="color:red;"></span>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="igv" style="font-weight: bold;">IGV</label>
                        <div class="form-check">
                            <input checked id="igv" name="igv" class="form-check-input"  type="checkbox" value="{{$igv}}" >
                            <label class="form-check-label" for="flexCheckDefault">
                                {{$igv.'%'}}
                            </label>
                            <input type="hidden" name="valor_igv" value="{{$igv}}">
                        </div>
                        <span class="igv_error msgError"  style="color:red;"></span> 
                        <span class="valor_igv_error msgError"  style="color:red;"></span>                                             
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="moneda" style="font-weight: bold;">MONEDA</label>
                        <select required name="moneda" id="moneda" data-placeholder="Seleccionar" class="select2_form" onchange="changeMoneda(this.value)">
                            <option value="PEN">SOLES</option>
                            <option value="USD">DÓLARES</option>
                        </select>
                        <span class="moneda_error msgError"  style="color:red;"></span>                       
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="tipo_cambio" id="lbl_tipo_cambio" style="font-weight: bold;">TIPO CAMBIO S/</label>
                        <i class="fa-solid fa-rotate btn btn-primary" onclick="getTipoCambio()"></i>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-hand-holding-dollar"></i>                            
                            </span>
                            <input required readonly  id="tipo_cambio" name="tipo_cambio" type="text" class="form-control inputDecimalPositivoLibre" placeholder="Tipo cambio" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                        <span class="tipo_cambio_error msgError"  style="color:red;"></span>                       
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
                        <span class="terminos_entrega_error msgError"  style="color:red;"></span>                       
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="proveedor" style="font-weight: bold;">PROVEEDOR</label> <i class="fa-solid fa-plus btn btn-primary" onclick="openMdlNuevoProveedor();"></i>
                        <select required name="proveedor" id="proveedor" data-placeholder="Seleccionar" class="select2_form">
                            @foreach ($proveedores as $proveedor)
                                <option value="{{$proveedor->id}}">{{$proveedor->tipo_documento_descripcion.':'.$proveedor->nro_documento.'-'.$proveedor->nombre}}</option>
                            @endforeach
                        </select>
                        <span class="proveedor_error msgError"  style="color:red;"></span>                       
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="direccion" class="required_field" style="font-weight: bold;">DIRECCIÓN DE OBRA</label>
                        <div class="form-floating">
                            <textarea required maxlength="200" id="direccion" name="direccion" class="form-control" placeholder="Leave a comment here" id="floatingTextarea">{{$proyecto->direccion}}</textarea>
                            <label for="floatingTextarea">Dirección</label>
                        </div>
                        <span class="direccion_error msgError"  style="color:red;"></span>                       
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="proyecto" class="required_field" style="font-weight: bold;">PROYECTO</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-diagram-project"></i>                            
                            </span>
                            <input value="{{$proyecto->nombre}}" required readonly  id="proyecto" name="proyecto" type="text" class="form-control inputDecimalPositivoLibre" placeholder="PROYECTO" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
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
                        <span class="modalidad_pago_error msgError"  style="color:red;"></span>                       
                    </div>

                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="proveedor" style="font-weight: bold;">DOCUMENTO</label>
                        <select required name="tipo_doc" id="tipo_doc" data-placeholder="Seleccionar" class="select2_form">
                            <option value="FACTURA">FACTURA</option>
                            <option value="BOLETA">BOLETA</option>
                        </select>
                        <span class="tipo_doc_error msgError"  style="color:red;"></span>                       
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="persona_contacto" class="required_field" style="font-weight: bold;">PERSONA DE CONTACTO</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-address-book"></i>                           
                            </span>
                            <select required name="persona_contacto" id="persona_contacto" data-placeholder="Seleccionar" class="select2_form">
                                <option value="{{$proyecto->supervisor_id}}">{{$proyecto->persona_contacto.' - '.'(S)'}}</option>
                                @foreach ($proyecto_personal as $personal)
                                    <option value="{{$personal->colaborador_id}}">{{$personal->persona_contacto}}</option>
                                @endforeach
                            </select>    
                        </div>
                        <span class="persona_contacto_error msgError"  style="color:red;"></span>                                           
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="observacion" style="font-weight: bold;">OBSERVACIÓN</label>
                        <div class="form-floating">
                            <textarea name="observacion" id="observacion" class="form-control" placeholder="Leave a comment here" maxlength="200"></textarea>
                            <label for="floatingTextarea">Observación</label>
                        </div>
                        <span class="observacion_error msgError"  style="color:red;"></span>                                           
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
    
                         
    
                            <div class="col-lg-3 col-md-5 col-sm-12 col-xs-12 d-none">
                                <label for="categoria" style="font-weight: bold;">UNIDAD</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </span>
                                    <input id="unidad" name="unidad" readonly type="text" class="form-control" placeholder="Unidad" aria-label="Username" aria-describedby="basic-addon1">
                                  </div>
                            </div>
    
                            <div class="col-lg-3 col-md-5 col-sm-12 col-xs-12">
                                <label for="precio" style="font-weight: bold;">PRECIO</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fa-solid fa-money-bill-1-wave"></i>                                    
                                    </span>
                                    <input id="precio" name="precio" type="text" class="form-control inputDecimalPositivo" placeholder="Precio" aria-label="Username" aria-describedby="basic-addon1">
                                  </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                <label for="categoria" style="font-weight: bold;">CANTIDAD</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fa-solid fa-box-open"></i>                                    
                                    </span>
                                    <input id="cantidad" name="cantidad" type="text" class="form-control inputDecimalPositivo" placeholder="Cantidad" aria-label="Username" aria-describedby="basic-addon1">
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
                                    @include('compras.cotizacion_compra.tables.table_cotizacion_to_orden_detalle')
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                @include('compras.registro_compra.tables.table_montos')       
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

           
    </div>
</form> 