

<form action="" id="formRegistroSalidaToGuiaRemision" method="post">    
    
        @csrf    
        
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 mb-3">
                <label for="razon_social" class="required_field" style="font-weight: bold;">REGISTRO SALIDA</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/documento/documento2.png')}}" alt="coins"/>
                    </span>
                    <input value="{{'RS-'.$registro_salida->id}}" readonly required maxlength="150" value="" name="razon_social" id="razon_social" type="text" class="form-control" placeholder="RAZÓN SOCIAL" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <span class="razon_social_error msgError"  style="color:red;"></span>
            </div> 
            <div class="col-12"></div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label for="emisor" class="required_field" style="font-weight: bold;">EMPRESA EMISORA</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/empresa/empresa3.png')}}" alt="coins"/>
                    </span>
                    <input value="{{$empresa->razon_social}}" readonly required maxlength="150" value="" name="emisor" id="emisor" type="text" class="form-control" placeholder="RAZÓN SOCIAL" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <span class="emisor_error msgError"  style="color:red;"></span>
            </div> 
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label for="destinatario" class="required_field" style="font-weight: bold;">DESTINATARIO</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/empresa/empresa3.png')}}" alt="coins"/>
                    </span>
                    <input value="{{$empresa->razon_social}}" readonly required maxlength="150" value="" name="destinatario" id="destinatario" type="text" class="form-control" placeholder="DESTINATARIO" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <span class="razon_social_error msgError"  style="color:red;"></span>
            </div> 
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label for="motivo_traslado" class="required_field" style="font-weight: bold;">MOTIVO TRASLADO</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/paquete/paquete1.png')}}" alt="coins"/>
                    </span>
                    <input type="hidden" value="04" name="motivo_traslado" id="motivo_traslado">
                    <input value="TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA" readonly required type="text" class="form-control" placeholder="MOTIVO TRASLADO" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <span class="motivo_traslado_error msgError"  style="color:red;"></span>
            </div> 
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 mb-3">
                <label for="serie" class="required_field" style="font-weight: bold;">SERIE</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/documento/documento2.png')}}" alt="coins"/>
                    </span>
                    <input value="T001" readonly required maxlength="10" name="serie" id="serie" type="text" class="form-control" placeholder="RAZÓN SOCIAL" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <span class="razon_social_error msgError"  style="color:red;"></span>
            </div> 
            <hr>
            <div class="col-12"></div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label for="fecha_emision" class="required_field" style="font-weight: bold;">FECHA EMISIÓN</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/date/date2.png')}}" alt="coins"/>
                    </span>
                    <input style="background-color: rgb(253, 253, 215);" value="<?php echo date('Y-m-d'); ?>"  required  name="fecha_emision" id="fecha_emision" type="date" class="form-control" placeholder="RAZÓN SOCIAL" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <span class="fecha_emision_error msgError"  style="color:red;"></span>
            </div> 
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label for="fecha_traslado" class="required_field" style="font-weight: bold;">FECHA TRASLADO</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/date/date2.png')}}" alt="coins"/>
                    </span>
                    <input style="background-color: rgb(253, 253, 215);" value="<?php echo date('Y-m-d'); ?>"  required  name="fecha_traslado" id="fecha_traslado" type="date" class="form-control" placeholder="RAZÓN SOCIAL" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <span class="fecha_traslado_error msgError"  style="color:red;"></span>
            </div> 
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <label for="unidad_medida_total" class="required_field" style="font-weight: bold;">UNIDAD MEDIDA TOTAL</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <img width="30" height="30" src="{{asset('img/icons/peso/peso1.png')}}" alt="coins"/>
                    </span>
                    <select required data-placeholder="Seleccione una opción" name="unidad_medida_total" id="unidad_medida_total" class="select2_form form-control">
                        <option value="KGM">KILOGRAMO (KGM)</option>
                        <option value="TNE">TONELADA (TNE)</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 mb-3">
                <label for="peso_total" class="required_field" style="font-weight: bold;">PESO TOTAL</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/peso/peso2.png')}}" alt="coins"/>
                    </span>
                    <input style="background-color: rgb(253, 253, 215);" value="1.00" required maxlength="10" name="peso_total" id="peso_total" type="text" class="form-control inputDecimalPositivo" placeholder="PESO TOTAL" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <span class="razon_social_error msgError"  style="color:red;"></span>
            </div> 
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 mb-3">
                <label for="peso_total" class="required_field" style="font-weight: bold;">N° BULTOS</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/paquete/paquete2.png')}}" alt="coins"/>
                    </span>
                    <input style="background-color: rgb(253, 253, 215);" value="1" required maxlength="10" name="peso_total" id="peso_total" type="text" class="form-control inputEnteroPositivo" placeholder="PESO TOTAL" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <span class="razon_social_error msgError"  style="color:red;"></span>
            </div> 
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-3">
                <label for="peso_total" class="required_field" style="font-weight: bold;">OBSERVACIÓN</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/texto/texto1.gif')}}" alt="coins"/>
                    </span>
                    <div class="form-floating">
                        <textarea style="background-color: rgb(253, 253, 215);" class="form-control" placeholder="Leave a comment here" id="floatingTextarea"></textarea>
                        <label for="floatingTextarea">OBSERVACIÓN</label>
                    </div>                </div>
                <span class="razon_social_error msgError"  style="color:red;"></span>
            </div> 
           
        </div>
        <hr>
        <div class="row">
            <div class="col-12 mt-3 mb-3">
                <div class="card">
                    <div class="card-header" style="background-color: rgb(0, 102, 255);font-weight:bold;color:white;">
                    DATOS ENVÍO
                    </div>
                    <div class="card-body">

                        <div class="row">     
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                <label for="motivo_traslado" class="required_field" style="font-weight: bold;">PUNTO PARTIDA</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">
                                        <img width="30" height="30" src="{{asset('img/icons_animados/transporte/transporte1.gif')}}" alt="coins"/>
                                    </span>
                                    <input type="hidden" value="04" name="motivo_traslado" id="motivo_traslado">
                                    <input value="TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA" readonly required type="text" class="form-control" placeholder="MOTIVO TRASLADO" aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                                <span class="motivo_traslado_error msgError"  style="color:red;"></span>
                            </div>  
                            
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                <label for="motivo_traslado" class="required_field" style="font-weight: bold;">PUNTO LLEGADA</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">
                                        <img width="30" height="30" src="{{asset('img/icons_animados/transporte/transporte2.gif')}}" alt="coins"/>
                                    </span>
                                    <input type="hidden" value="04" name="motivo_traslado" id="motivo_traslado">
                                    <input value="TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA" readonly required type="text" class="form-control" placeholder="MOTIVO TRASLADO" aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                                <span class="motivo_traslado_error msgError"  style="color:red;"></span>
                            </div>  
                        </div>
                                
                    </div>
                </div>
            </div>
        </div>  
        

        <div class="row">
            <div class="col-12 mt-3 mb-3">
                <div class="card">
                    <div class="card-header" style="background-color: rgb(0, 102, 255);font-weight:bold;color:white;">
                    DATOS MODO DE TRASLADO
                    </div>
                    <div class="card-body">

                           <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                    <label for="modo_traslado" class="required_field" style="font-weight: bold;">MODO TRASLADO</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <img width="30" height="30" src="{{asset('img/icons/transporte/transporte2.png')}}" alt="coins"/>
                                        </span>
                                        <input value="TRANSPORTE PRIVADO" readonly required  name="modo_traslado" id="modo_traslado" type="text" class="form-control" placeholder="TIPO TRANSPORTE" aria-label="Username" aria-describedby="basic-addon1">
                                    </div>
                                    <span class="tipo_transporte_error msgError"  style="color:red;"></span>
                                </div> 
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                    <label for="conductor" class="required_field" style="font-weight: bold;">CONDUCTOR</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <img width="30" height="30" src="{{asset('img/icons/conductor/conductor2.png')}}" alt="coins"/>
                                        </span>
                                        <select required data-placeholder="Seleccione una opción" name="conductor" id="conductor" class="select2_form form-control">
                                            @foreach ($conductores as $conductor)
                                                <option value="{{$conductor->id}}">{{$conductor->nombre.'-'.$conductor->tipo_documento_nombre.':'.$conductor->nro_documento}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                    <label for="vehiculo" class="required_field" style="font-weight: bold;">VEHÍCULO</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <img width="30" height="30" src="{{asset('img/icons/transporte/transporte3.png')}}" alt="coins"/>
                                        </span>
                                        <select required data-placeholder="Seleccione una opción" name="vehiculo" id="vehiculo" class="select2_form" >
                                            @foreach ($vehiculos as $vehiculo)
                                                <option value="{{$vehiculo->id}}">{{$vehiculo->placa.': '.$vehiculo->modelo.' - '.$vehiculo->marca}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                           </div>

                           
                                
                           

                      
                       
                    </div>
                </div>
            </div>
        </div>  
        
        <div class="row mt-3">
            <div class="col-12 mt-3 mb-3">
                <div class="card">
                    <div class="card-header" style="background-color: rgb(0, 102, 255);font-weight:bold;color:white;">
                    DETALLE DE LA GÚIA DE REMISIÓN
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    @include('logistica.registro_salida.tables.table_registro_salida_to_guia_detalle')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

           
   
</form> 