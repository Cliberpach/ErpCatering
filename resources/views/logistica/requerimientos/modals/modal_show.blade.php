<div class="modal fade" id="mdlShowRequerimiento" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Encabezado de la tarjeta -->
            <div class="modal-header text-light">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ver Requerimiento</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Cuerpo de la tarjeta -->
            <div class="modal-body">
                <div class="row">
                    <!-- Sección de Información del Producto -->
                    <div class="col-12">
                        <div class="border rounded p-4 bg-white shadow-sm">
                            <h5 class="text-primary border-bottom pb-2 mb-3">Detalles del Requerimiento</h5>
                            
                            <!-- Mostrar los datos -->
                            <div class="row mb-3">
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">PROYECTO:</strong>
                                    <span class="text-muted" id="spanProyecto"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">SUPERVISOR:</strong>
                                    <span class="text-muted" id="spanSupervisor"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">ORDEN COMPRA:</strong>
                                    <span class="text-muted" id="spanOrdenCompra"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">FACTURA ATENCIÓN:</strong>
                                    <span class="text-muted" id="spanFacturaAtencion"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">FECHA ATENCIÓN:</strong>
                                    <span class="text-muted" id="spanFechaAtencion"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">FECHA REGISTRO:</strong>
                                    <span class="text-muted" id="spanFechaRegistro"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">ESTADO:</strong>
                                    <span class="text-muted" id="spanEstado"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Stocks (se puede agregar después) -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="border rounded bg-white shadow-sm">
                            <h6 class="text-primary mb-3 border-bottom p-2">DETALLE</h6>
                            <div class="table-responsive">
                                @include('logistica.requerimientos.tables.table_requerimiento_show')
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

    let dtRequerimientoShow    =   null;

    async function openMdlShowRequerimiento(id){
        await getShowRequerimiento(id);
        $('#mdlShowRequerimiento').modal('show');
    }

    $('#mdlShowRequerimiento').on('hidden.bs.modal', function (e) {
        limpiarModalShow();
    });


    async function getShowRequerimiento(requerimiento_id){
        toastr.clear();
        const token                 =   document.querySelector('input[name="_token"]').value;
        const urlShowRequerimiento  =   `{{ route('logistica.requerimientos.show', ':id') }}`.replace(':id', requerimiento_id);

        try {
            mostrarAnimacion1();
            const response  =   await fetch(urlShowRequerimiento, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                    });

            const   res =   await response.json();
                                
            if(res.success){
                pintarRequerimiento(res.requerimiento);
                limpiarTabla('table_requerimiento_show');
                destruirDataTable(dtRequerimientoShow);
                pintarTableRequerimientoShow(res.requerimiento_detalle);
                iniciarDataTableRequerimientoShow();
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

    function iniciarDataTableRequerimientoShow(){
        dtRequerimientoShow  =   new DataTable('#table_requerimiento_show',{
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

    function pintarRequerimiento(requerimiento){
        document.querySelector('#spanProyecto').textContent         =   requerimiento.proyecto_nombre;
        document.querySelector('#spanSupervisor').textContent       =   requerimiento.supervisor_nombre;
        document.querySelector('#spanOrdenCompra').textContent      =   requerimiento.orden_compra_id?requerimiento.orden_compra_id:'-';
        document.querySelector('#spanFacturaAtencion').textContent  =   requerimiento.factura_atencion?requerimiento.factura_atencion:'-';
        document.querySelector('#spanFechaAtencion').textContent    =   requerimiento.fecha_atencion?requerimiento.fecha_atencion:'-';
        document.querySelector('#spanFechaRegistro').textContent    =   requerimiento.fecha_registro;
        document.querySelector('#spanEstado').textContent           =   requerimiento.estado;

    }

    function pintarTableRequerimientoShow(requerimiento_detalle){
        let filas   =   ``;
        const tbody =   document.querySelector('#table_requerimiento_show tbody');
        requerimiento_detalle.forEach((rd)=>{
            filas   +=  `<tr>
                            <th>${rd.producto_nombre}</th>
                            <td>${rd.categoria_nombre}</td>
                            <td>${rd.marca_nombre}</td>
                            <td>${rd.producto_unidad_medida}</td>
                            <td>${rd.cantidad}</td>
                        </tr>`;
        })
        tbody.innerHTML =   filas;
    }

    function limpiarModalShow(){
        document.querySelector('#spanCategoria').textContent    =   '';
        document.querySelector('#spanMarca').textContent        =   '';
        document.querySelector('#spanProducto').textContent     =   '';
        document.querySelector('#spanPrecio').textContent       =   '';
        document.querySelector('#spanUnidad').textContent       =   '';
    }
</script>