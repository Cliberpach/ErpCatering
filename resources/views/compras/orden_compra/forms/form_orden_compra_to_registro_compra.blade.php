<form action="" id="formOrdenCompraToRegistroCompra" method="post">    
    @csrf   
    
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required_field" for="fecha_emision" style="font-weight: bold;">FECHA EMISIÓN</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('icons/date/date2.png')}}" alt="coins"/>
                        </span>
                        <input style="background:rgb(255, 255, 220);" value="<?php echo date('Y-m-d'); ?>" required type="date" name="fecha_emision" id="fecha_emision" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required_field" for="fecha_entrega" style="font-weight: bold;">FECHA ENTREGA</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('icons/date/date2.png')}}" alt="coins"/>
                        </span>
                        <input style="background:rgb(255, 255, 220);" value="<?php echo date('Y-m-d'); ?>" required type="date" name="fecha_entrega" id="fecha_entrega" class="form-control">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required_field" for="proveedor" style="font-weight: bold;">PROVEEDOR</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('icons/proveedor/proveedor1.png')}}" alt="coins"/>
                        </span>
                        <input readonly value="{{$orden_compra->tipo_documento_nombre.":".$orden_compra->nro_documento."-".$orden_compra->proveedor_nombre}}" required type="text" name="proveedor" id="proveedor" class="form-control">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="observacion" style="font-weight: bold;">OBSERVACIÓN</label>
                    <div class="form-floating">
                        <textarea name="observacion" style="background:rgb(255, 255, 220);" class="form-control" placeholder="Leave a comment here" id="floatingTextarea"></textarea>
                        <label for="floatingTextarea">Observación</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required_field" for="moneda" style="font-weight: bold;">MONEDA</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('icons/money/money1.png')}}" alt="coins"/>
                        </span>
                        <input readonly value="{{$orden_compra->moneda}}" required id="moneda" name="moneda" type="text" class="form-control" placeholder="Moneda" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="tipo_cambio" id="lbl_tipo_cambio" style="font-weight: bold;">TIPO CAMBIO S/</label>
                    <i class="fa-solid fa-rotate btn btn-primary" onclick="getTipoCambio()"></i>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('icons/money/tipo_cambio.png')}}" alt="coins"/>
                        </span>
                        <input value="{{$orden_compra->tipo_cambio}}" required readonly  id="tipo_cambio" name="tipo_cambio" type="text" class="form-control inputDecimalPositivoLibre" placeholder="Tipo cambio" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required_field" for="tipo_doc" style="font-weight: bold;">TIPO DOC</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('icons/invoice/invoice2.png')}}" alt="coins"/>
                        </span>
                        <input value="{{$orden_compra->documento}}" required readonly  id="tipo_doc" name="tipo_doc" type="text" class="form-control" placeholder="Tipo Documento" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="igv" style="font-weight: bold;">IGV</label>
                    <div class="form-check">
                        
                        <input disabled
                        @if ($orden_compra->precios_igv == 1) checked @endif 
                        id="igv" name="igv" class="form-check-input"  type="checkbox" value="18" >
                        <label class="form-check-label" for="flexCheckDefault">
                            18%
                        </label>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required_field" for="serie" style="font-weight: bold;">SERIE</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('icons/invoice/invoice1.png')}}" alt="coins"/>
                        </span>
                        <input style="background:rgb(255, 255, 220);" required id="serie" name="serie" type="text" class="form-control" placeholder="Serie" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required_field" for="numero" style="font-weight: bold;">N°</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">
                            <img width="30" height="30" src="{{asset('icons/hash/hash1.png')}}" alt="coins"/>
                        </span>
                        <input style="background:rgb(255, 255, 220);" required id="numero" name="numero" type="text" class="form-control inputEnteroPositivo" placeholder="Número" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
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
                                @include('compras.orden_compra.tables.table_orden_compra_to_registro_compra')
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                            
                              
                            @include('compras.orden_compra.tables.table_orden_compra_to_registro_compra_montos')
                           
                                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

       
</form> 