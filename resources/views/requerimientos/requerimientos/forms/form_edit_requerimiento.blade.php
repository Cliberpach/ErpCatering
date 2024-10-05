<form action="" id="formActualizarRequerimiento" method="post">    
        @csrf      

        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label for="proyecto" style="font-weight: bold;" class="required_field">PROYECTO</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-diagram-project"></i>
                    </span>
                    <input value="{{$proyecto->nombre}}" readonly name="proyecto" id="proyecto" type="text" class="form-control" placeholder="PROYECTO" aria-label="Username" aria-describedby="basic-addon1">
                </div>        
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label for="supervisor"  style="font-weight: bold;" class="required_field">SUPERVISOR</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-user-tie"></i>
                    </span>
                    <input value="{{$colaborador->nombre}}" readonly name="supervisor" id="supervisor" type="text" class="form-control" placeholder="SUPERVISOR" aria-label="Username" aria-describedby="basic-addon1">
                  </div>        
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label for="proveedor" style="font-weight: bold;">PROVEEDOR SUGERIDO</label> <i class="fa-solid fa-plus btn btn-primary" onclick="openMdlNuevoProveedor();"></i>
                <select name="proveedor" id="proveedor" data-placeholder="Seleccionar" class="select2_form">
                    @foreach ($proveedores as $proveedor)
                        <option value="{{$proveedor->id}}">{{$proveedor->tipo_documento_descripcion.':'.$proveedor->nro_documento.'-'.$proveedor->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label for="fecha_atencion" style="font-weight: bold;" class="required_field">FECHA ATENCIÓN</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-calendar-days"></i>
                    </span>
                    <input value="{{$requerimiento->fecha_atencion}}" required name="fecha_atencion" id="fecha_atencion" type="date" class="form-control"  aria-label="Username" aria-describedby="basic-addon1">
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
                    DETALLE DE LA COTIZACIÓN
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    @include('requerimientos.requerimientos.tables.table_requerimiento_detalle')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   
</form> 