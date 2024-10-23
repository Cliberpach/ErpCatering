<form action="" method="post" id="formRegistrarCotizacionCompuesta">
    @csrf

    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="fecha_registro" class="required_field" style="font-weight: bold;">FECHA REGISTRO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-calendar-days"></i>
                </span>
                <input value="{{ date('Y-m-d') }}" readonly required id="fecha_registro" name="fecha_registro" type="date" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="proyecto" class="required_field" style="font-weight: bold;">PROYECTO</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-diagram-project"></i>
                </span>
                <input value="{{$proyecto->proyecto_nombre}}" readonly required id="proyecto" name="proyecto" type="text" class="form-control inputEnteroPositivo" placeholder="Proyecto" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="proyecto" class="required_field" style="font-weight: bold;">REGISTRADOR</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-diagram-project"></i>
                </span>
                <input value="{{$colaborador_registrador->colaborador_nombre}}" readonly required id="proyecto" name="proyecto" type="text" class="form-control inputEnteroPositivo" placeholder="Proyecto" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <label for="" style="font-weight: bold;">REQUERIMIENTOS</label>
            <div class="table-responsive">
                @include('compras.cotizacion_compra.tables.table_compuesta_requerimientos')
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <label for="" style="font-weight: bold;">DETALLES</label>
            <div class="table-responsive">
               @include('compras.cotizacion_compra.tables.table_compuesta_requerimiento_detalle')
            </div>
        </div>

    </div>

    <hr>
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
                                    @include('compras.cotizacion_compra.tables.table_compra_detalle')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

</form>