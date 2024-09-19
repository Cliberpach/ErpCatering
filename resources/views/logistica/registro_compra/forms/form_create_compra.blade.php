<form action="" id="formRegistrarCompra" method="post">    
        @csrf   
        
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="fecha_emision" style="font-weight: bold;">FECHA EMISIÓN</label>
                        <input value="<?php echo date('Y-m-d'); ?>" required type="date" name="fecha_emision" id="fecha_emision" class="form-control">
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="fecha_entrega" style="font-weight: bold;">FECHA ENTREGA</label>
                        <input value="<?php echo date('Y-m-d'); ?>" required type="date" name="fecha_entrega" id="fecha_entrega" class="form-control">
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="proveedor" style="font-weight: bold;">PROVEEDOR</label>
                        <select required name="proveedor" id="proveedor" data-placeholder="Seleccionar" class="select2_form">
                            @foreach ($proveedores as $proveedor)
                                <option value="{{$proveedor->id}}">{{$proveedor->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="observacion" style="font-weight: bold;">OBSERVACIÓN</label>
                        <div class="form-floating">
                            <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea"></textarea>
                            <label for="floatingTextarea">Observación</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="moneda" style="font-weight: bold;">MONEDA</label>
                        <select required name="moneda" id="moneda" data-placeholder="Seleccionar" class="select2_form">
                            <option value="SOLES">SOLES</option>
                            <option value="DÓLARES">DÓLARES</option>
                        </select>
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
                    </div>

                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="proveedor" style="font-weight: bold;">TIPO DOC</label>
                        <select name="tipo_doc" id="tipo_doc" data-placeholder="Seleccionar" class="select2_form">
                            <option value="FACTURA">FACTURA</option>
                            <option value="BOLETA">BOLETA</option>
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label for="igv" style="font-weight: bold;">IGV</label>
                        <div class="form-check">
                            <input id="igv" name="igv" class="form-check-input"  type="checkbox" value="18" >
                            <label class="form-check-label" for="flexCheckDefault">
                                18%
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="serie" style="font-weight: bold;">SERIE</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-envelopes-bulk"></i>
                            </span>
                            <input required id="serie" name="serie" type="text" class="form-control" placeholder="Serie" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required_field" for="numero" style="font-weight: bold;">N°</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-hashtag"></i>
                            </span>
                            <input required id="numero" name="numero" type="text" class="form-control inputEnteroPositivo" placeholder="Número" aria-label="Username" aria-describedby="basic-addon1">
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
                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                <label for="almacen" style="font-weight: bold;">ALMACÉN</label>
                                <div class="input-group mb-3">
                                    <select name="almacen" id="almacen" data-placeholder="Seleccionar" class="select2_form">
                                        @foreach ($almacenes as $almacen)
                                            <option value="{{$almacen->id}}">{{$almacen->descripcion}}</option> 
                                        @endforeach
                                    </select>
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
                                    @include('logistica.registro_compra.tables.table_compra_detalle')
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                
                                  
                                @include('logistica.registro_compra.tables.table_montos')
                               
                                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

           
</form> 