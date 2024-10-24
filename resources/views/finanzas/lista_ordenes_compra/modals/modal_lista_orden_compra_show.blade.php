<div class="modal fade" id="mdlListShowOrdenCompra" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Encabezado de la tarjeta -->
            <div class="modal-header text-light">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ver Orden Compra</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Cuerpo de la tarjeta -->
            <div class="modal-body">

                <div class="row mb-2">
                    <div class="col-12">
                        <div class="border rounded p-4 bg-white shadow-sm">
                            <h5 class="text-primary border-bottom pb-2 mb-3" id="orden_compra_id"></h5>
                            
                            <!-- Mostrar los datos -->
                            <div class="row">
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-user-pen"></i> REGISTRADOR:
                                    </strong>
                                    <span class="text-muted" id="spanRegistrador"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-truck-fast"></i> PROVEEDOR:
                                    </strong>
                                    <span class="text-muted" id="spanProveedor"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-money-bill"></i> MODALIDAD PAGO:
                                    </strong>
                                    <span class="text-muted" id="spanModalidadPago"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-diagram-project"></i> PROYECTO:
                                    </strong>
                                    <span class="text-muted" id="spanProyecto"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-file-invoice"></i> DOCUMENTO:
                                    </strong>
                                    <span class="text-muted" id="spanDocumento"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-map-location-dot"></i> DIRECCIÓN OBRA:
                                    </strong>
                                    <span class="text-muted" id="spanDireccionObra"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-keyboard"></i> OBSERVACIÓN:
                                    </strong>
                                    <span class="text-muted" id="spanObservacion"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-address-book"></i> PERSONA CONTACTO:
                                    </strong>
                                    <span class="text-muted" id="spanPersonaContacto"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-calendar-days"></i> FECHA ENTREGA:
                                    </strong>
                                    <span class="text-muted" id="spanFechaEntrega"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-calendar-days"></i> FECHA REGISTRO:
                                    </strong>
                                    <span class="text-muted" id="spanFechaRegistro"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-handshake"></i> TÉRMINOS ENTREGA:
                                    </strong>
                                    <span class="text-muted" id="spanTerminosEntrega"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-coins"></i> MONEDA:
                                    </strong>
                                    <span class="text-muted" id="spanMoneda"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-tags"></i> PRECIOS CON IGV: 
                                    </strong>
                                    <span class="text-muted" id="spanPreciosConIgv"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-percent"></i> IGV: 
                                    </strong>
                                    <span class="text-muted" id="spanPorcentajeIGV"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-money-bill-1-wave"></i> SUBTOTAL: 
                                    </strong>
                                    <span id="spanSubtotal" style="color: black;font-weight:bold;"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-money-bill-1-wave"></i> MONTO IGV: 
                                    </strong>
                                    <span id="spanMontoIgv" style="color: black;font-weight:bold;"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-money-bill-1-wave"></i> TOTAL: 
                                    </strong>
                                    <span id="spanTotal" style="color: black;font-weight:bold;"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-chart-simple"></i> ESTADO:
                                    </strong>
                                    <span id="spanEstado"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-2" id="boxCotizacionOrigen" style="display: none;">
                    <div class="col-12">
                        <div class="border rounded p-4 bg-white shadow-sm">

                            <div class="row">
                                <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12" style="display:flex;align-items:center;">
                                    <h5 class="text-primary" id="cotizacion_compra_id">
                                        Cotización de Origen
                                    </h5>
                                </div>
                                <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12" style="text-align:end;">
                                    <a class="btn btn-danger a_pdf_cotizacion_compra" target="_blank">
                                        <i class="fa-solid fa-file-pdf"></i> PDF
                                    </a>
                                </div>
                            </div>
                           
                            <hr>
                            
                            <!-- Mostrar los datos -->
                            <div class="row">
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-user-pen"></i> REGISTRADOR:
                                    </strong>
                                    <span class="text-muted" id="spanRegistradorCotizacion"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-user-tie"></i> SUPERVISOR:
                                    </strong>
                                    <span class="text-muted" id="spanSupervisorCotizacion"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-diagram-project"></i> PROYECTO:
                                    </strong>
                                    <span class="text-muted" id="spanProyectoCotizacion"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-calendar-days"></i> FECHA REGISTRO:
                                    </strong>
                                    <span class="text-muted" id="spanFechaRegistroCotizacion"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-chart-simple"></i> ESTADO:
                                    </strong>
                                    <span id="spanEstadoCotizacion"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="boxRequerimientoOrigen" style="display: none;">
                    <div class="col-12">
                        <div class="border rounded p-4 bg-white shadow-sm">
                            <h5 class="text-primary pb-2 mb-3 border-bottom" id="requerimiento_id">Requerimiento Origen</h5>
                            
                            <!-- Mostrar los datos -->
                            <div class="row">
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-diagram-project"></i> PROYECTO:
                                    </strong>
                                    <span class="text-muted" id="spanProyectoRequerimiento"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-user-tie"></i> SUPERVISOR:
                                    </strong>
                                    <span class="text-muted" id="spanSupervisorRequerimiento"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-diagram-project"></i> PROVEEDOR SUGERIDO:
                                    </strong>
                                    <span class="text-muted" id="spanProveedorRequerimiento"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-calendar-days"></i> FECHA ATENCIÓN SOLICITADA:
                                    </strong>
                                    <span class="text-muted" id="spanFechaAtencionSolicitadaRequerimiento"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-calendar-days"></i> FECHA REGISTRO:
                                    </strong>
                                    <span class="text-muted" id="spanFechaRegistroRequerimiento"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">
                                        <i class="fa-solid fa-chart-simple"></i> ESTADO:
                                    </strong>
                                    <span id="spanEstadoRequerimiento"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="row mt-4">
                    <div class="col-12">
                        <div class="border rounded bg-white shadow-sm">
                            <h6 class="text-primary mb-3 border-bottom p-2">DETALLE</h6>
                            <div class="table-responsive">
                                @include('compras.orden_compra.tables.table_orden_compra_show')
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie de la tarjeta -->
            <div class="modal-footer text-white d-flex justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>

    let dtOrdenCompraShow    =   null;

    async function openMdlShowOrdenCompra(id){
        await getShowOrdenCompra(id);
        $('#mdlListShowOrdenCompra').modal('show');
    }

    $('#mdlListShowOrdenCompra').on('hidden.bs.modal', function (e) {
        limpiarModalShow();
    });


    async function getShowOrdenCompra(orden_compra_id){
        toastr.clear();
        const token                     =   document.querySelector('input[name="_token"]').value;
        const urlShowOrdenCompra        =   `{{ route('finanzas.lista_orden_compra.show', ':id') }}`.replace(':id', orden_compra_id);
        const boxCotizacionOrigen       =   document.querySelector('#boxCotizacionOrigen');
        const boxRequerimientoOrigen    =   document.querySelector('#boxRequerimientoOrigen');

        try {
            mostrarAnimacion1();

            boxCotizacionOrigen.style.display       =   'none';
            boxRequerimientoOrigen.style.display    =   'none';

            const response  =   await fetch(urlShowOrdenCompra, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                    });

            const   res =   await response.json();
                                
            if(res.success){
                console.log(res);
                pintarOrdenCompra(res.orden_compra);

                if(res.cotizacion_compra){
                    boxCotizacionOrigen.style.display   =   'flex';
                    pintarCotizacionCompraOrigen(res.cotizacion_compra);
                    const aPdfCotizacion    =   document.querySelector('.a_pdf_cotizacion_compra');
                    aPdfCotizacion.href     =   'javascript:void(0);';
                    const urlPdf            = `{{ route('compras.cotizacion_compra.pdf', ':id') }}`.replace(':id', res.cotizacion_compra.id);
                    aPdfCotizacion.href     =   urlPdf;
                }

                if(res.requerimiento){
                    boxRequerimientoOrigen.style.display   =   'flex';
                    pintarRequerimientoOrigen(res.requerimiento);
                }

                limpiarTabla('table_orden_compra_show');
                destruirDataTable(dtOrdenCompraShow);
                pintarTableOrdenCompraShow(res.orden_compra.moneda,res.orden_compra_detalle);
                iniciarDataTableOrdenCompraShow();
                toastr.success(res.message,'OPERACIÓN COMPLETADA');

            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR');
            }

        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VER REQUERIMIENTO');
        }finally{
            ocultarAnimacion1();
        }
          
    }

    function iniciarDataTableOrdenCompraShow(){
        dtOrdenCompraShow  =   new DataTable('#table_orden_compra_show',{
            language: {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "emptyTable": "No hay datos disponibles en la tabla",
                "aria": {
                    "sortAscending": ": activar para ordenar la columna de manera ascendente",
                    "sortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }


    function pintarOrdenCompra(orden_compra){
        document.querySelector('#orden_compra_id').textContent      =   `ORDEN DE COMPRA N°${orden_compra.id}`;
        document.querySelector('#spanRegistrador').textContent      =   orden_compra.colaborador_registrador_nombre;
        document.querySelector('#spanProveedor').textContent        =   `${orden_compra.proveedor_nombre}-${orden_compra.proveedor_tipo_documento}:${orden_compra.proveedor_nro_documento}`;
        document.querySelector('#spanModalidadPago').textContent    =   orden_compra.modalidad_pago_nombre;
        document.querySelector('#spanProyecto').textContent         =   orden_compra.proyecto_nombre;
        document.querySelector('#spanDocumento').textContent        =   orden_compra.documento;
        document.querySelector('#spanDireccionObra').textContent    =   orden_compra.direccion_obra;
        document.querySelector('#spanObservacion').textContent      =   orden_compra.observacion;
        document.querySelector('#spanPersonaContacto').innerHTML    =   `<div class="row">
                                                                            <div class="col-12">${orden_compra.persona_contacto_nombre}</div>
                                                                            <div class="col-12"><i style="color:black;" class="fa-solid fa-phone-volume"></i> ${orden_compra.persona_contacto_telefono}</div>
                                                                        </div>`;
        document.querySelector('#spanFechaEntrega').textContent     =   orden_compra.fecha_entrega;
        document.querySelector('#spanFechaRegistro').textContent    =   orden_compra.created_at;
        document.querySelector('#spanTerminosEntrega').textContent  =   orden_compra.terminos_entrega;
        document.querySelector('#spanMoneda').textContent           =   orden_compra.moneda;

        document.querySelector('#spanEstado').className             =   '';
        if(orden_compra.estado === 'PENDIENTE'){
            document.querySelector('#spanEstado').classList.add('badge','bg-danger');
        }
        if(orden_compra.estado === 'FACTURADO'){
            document.querySelector('#spanEstado').classList.add('badge','bg-primary');
        }
        document.querySelector('#spanEstado').textContent           =   orden_compra.estado;

        document.querySelector('#spanPreciosConIgv').textContent    =   orden_compra.precios_igv == 1?'SI':'NO';
        document.querySelector('#spanPorcentajeIgv').textContent    =   Number(orden_compra.igv).toFixed(2);
        
        document.querySelector('#spanSubtotal').textContent         =   Number(orden_compra.subtotal).toFixed(2);
        document.querySelector('#spanMontoIgv').textContent         =   Number(orden_compra.monto_igv).toFixed(2);
        document.querySelector('#spanTotal').textContent            =   Number(orden_compra.total).toFixed(2);

    }

    function pintarCotizacionCompraOrigen(cotizacion_compra){

        document.querySelector('#cotizacion_compra_id').textContent         =   `COTIZACIÓN ORIGEN N°${cotizacion_compra.id}`;
        document.querySelector('#spanRegistradorCotizacion').textContent    =   cotizacion_compra.colaborador_registrador_nombre;   
        document.querySelector('#spanSupervisorCotizacion').textContent     =   cotizacion_compra.supervisor_nombre;   
        document.querySelector('#spanProyectoCotizacion').textContent       =   cotizacion_compra.proyecto_nombre;   
        document.querySelector('#spanFechaRegistroCotizacion').textContent  =   cotizacion_compra.fecha_registro;   
        document.querySelector('#spanEstadoCotizacion').textContent         =   cotizacion_compra.estado;   

    }

    function pintarRequerimientoOrigen(requerimiento) {
        document.querySelector('#requerimiento_id').textContent             =   `REQUERIMIENTO ORIGEN N°${requerimiento.id}`;
        document.querySelector('#spanProyectoRequerimiento').textContent    =   requerimiento.proyecto_nombre;   
        document.querySelector('#spanSupervisorRequerimiento').textContent  =   requerimiento.supervisor_nombre;   
        document.querySelector('#spanProveedorRequerimiento').textContent   =   requerimiento.proveedor_nombre;   
        document.querySelector('#spanFechaAtencionSolicitadaRequerimiento').textContent =   requerimiento.fecha_atencion;   
        document.querySelector('#spanFechaRegistroRequerimiento').textContent           =   requerimiento.fecha_registro;   
        document.querySelector('#spanEstadoRequerimiento').textContent      =   requerimiento.estado;   
    }


    function pintarTableOrdenCompraShow(moneda,orden_compra_detalle){
        let filas   =   ``;
        const tbody =   document.querySelector('#table_orden_compra_show tbody');
        orden_compra_detalle.forEach((oc)=>{
            filas   +=  `<tr>
                            <th>${oc.producto_nombre}</th>
                            <td>${oc.categoria_nombre}</td>
                            <td>${oc.marca_nombre}</td>
                            <td>${oc.producto_unidad_medida}</td>
                        `;

            if(moneda === 'PEN'){
                filas +=    `<td>${oc.precio_soles}</td>`;
            }
            if(moneda === 'USD'){
                filas +=    `<td>${oc.precio_dolares}</td>`;
            }

            filas +=    `<td>${oc.cantidad}</td>`;

            if(moneda === 'PEN'){
                filas +=    `<td>${oc.cantidad * oc.precio_soles}</td>`;
            }
            if(moneda === 'USD'){
                filas +=    `<td>${oc.cantidad * oc.precio_dolares}</td>`;
            }

            filas += `</tr>`;

        })
        tbody.innerHTML =   filas;
    }

    function limpiarModalShow(){
        document.querySelector('#spanRegistrador').textContent    =   '';
        document.querySelector('#spanProveedor').textContent      =   '';
        document.querySelector('#spanModalidadPago').textContent  =   '';
        document.querySelector('#spanProyecto').textContent       =   '';
        document.querySelector('#spanDocumento').textContent      =   '';
    }
</script>