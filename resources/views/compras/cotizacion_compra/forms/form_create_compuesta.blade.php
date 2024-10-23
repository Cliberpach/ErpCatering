<form action="" method="post" id="formRegistrarCotizacionCompuesta">
    @csrf

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