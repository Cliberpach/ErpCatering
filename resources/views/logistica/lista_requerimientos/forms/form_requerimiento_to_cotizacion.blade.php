<form action="" id="formRequerimientoToCotizacionCompra" method="post">    
    <div class="row">
        @csrf      
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <label for="proyecto" class="required_field" style="font-weight: bold;">PROYECTO</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-diagram-project"></i>
                    </span>
                    <input value="{{$requerimiento->proyecto_nombre}}" readonly id="proyecto" name="proyecto" type="text" class="form-control" placeholder="Proyecto" aria-label="Username" aria-describedby="basic-addon1">
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <label for="supervisor" class="required_field" style="font-weight: bold;">SUPERVISOR</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-user-tie"></i>                    
                    </span>
                    <input value="{{$requerimiento->supervisor_nombre}}" readonly id="supervisor" name="supervisor" type="text" class="form-control" placeholder="Supervisor" aria-label="Username" aria-describedby="basic-addon1">
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <label for="proveedor" class="required_field" style="font-weight: bold;">PROVEEDOR SUGERIDO</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-truck-field"></i>                
                    </span>
                    <input value="{{$requerimiento->proveedor_nombre}}" readonly id="proveedor" name="proveedor" type="text" class="form-control" placeholder="Proveedor sugerido" aria-label="Username" aria-describedby="basic-addon1">
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <label for="registrador" class="required_field" style="font-weight: bold;">REGISTRADOR</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-user-pen"></i>                
                    </span>
                    <input  value="{{Auth::user()->name}}" readonly id="registrador" name="registrador" type="text" class="form-control" placeholder="Registrador" aria-label="Username" aria-describedby="basic-addon1">
                    <input type="hidden" value="{{Auth::user()->id}}" id="registrador_id" name="registrador_id">
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <label for="fecha_registro" class="required_field" style="font-weight: bold;">FECHA REGISTRO</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-calendar-days"></i>               
                    </span>
                    <input value="<?php echo date('Y-m-d'); ?>" readonly id="fecha_registro" name="fecha_registro" type="date" class="form-control" placeholder="Registrador" aria-label="Username" aria-describedby="basic-addon1">
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
                                    @include('logistica.lista_requerimientos.tables.table_requerimiento_to_cotizacion_detalle')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

           
    </div>
</form> 