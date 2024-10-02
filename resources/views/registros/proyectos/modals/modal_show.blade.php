<div class="modal fade" id="mdlShowProyecto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Encabezado de la tarjeta -->
            <div class="modal-header text-light">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ver Proyecto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Cuerpo de la tarjeta -->
            <div class="modal-body">
                <div class="row mb-3">
                    <!-- Sección de Información del Producto -->
                    <div class="col-12">
                        <div class="border rounded p-4 bg-white shadow-sm">
                            <h5 class="text-primary border-bottom mb-2">Detalles del Proyecto</h5>
                            
                            <!-- Mostrar los datos -->
                            <div class="row">
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">Nombre:</strong>
                                    <span class="text-muted" id="spanNombre"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">Costo:</strong>
                                    <span class="text-muted" id="spanCosto"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">Avance Costo:</strong>
                                    <span class="text-muted" id="spanAvanceCosto"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">Diferencia:</strong>
                                    <span class="text-muted" id="spanDiferencia"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">SUPERVISOR:</strong>
                                    <span class="text-muted" id="spanSupervisor"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Stocks (se puede agregar después) -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="border rounded p-4 bg-white shadow-sm">
                            <h6 class="text-primary mb-3 border-bottom pb-2">PERSONAL</h6>
                            <div class="table-responsive">
                                @include('registros.proyectos.tables.table_show_proyecto_personal')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <div class="border rounded p-4 bg-white shadow-sm">
                            <h6 class="text-primary mb-3 border-bottom pb-2">MAQUINARIA</h6>
                            <div class="table-responsive">
                                @include('registros.proyectos.tables.table_show_proyecto_maquinaria')
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

    let dtShowProyectoPersonal      =   null;
    let dtShowProyectoMaquinaria    =   null;


    function eventsMdlShowProyecto(){
        $('#mdlShowProyecto').on('hidden.bs.modal', function (e) {
            limpiarModalShow();
        });
    }

    async function openMdlShowProyecto(id){
        await getShowProyecto(id);
        $('#mdlShowProyecto').modal('show');
    }

   

    function iniciarDataTableShowProyectoPersonal(){
        dtShowProyectoPersonal  =   new DataTable('#table_show_proyecto_personal',{
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

    function iniciarDataTableShowProyectoMaquinaria(){
        dtShowProyectoMaquinaria  =   new DataTable('#table_show_proyecto_maquinaria',{
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


    async function getShowProyecto(proyecto_id){
        toastr.clear();
        const token                 =   document.querySelector('input[name="_token"]').value;
        const urlShowProyecto       =   `{{ route('registros.proyecto.show', ':id') }}`.replace(':id', proyecto_id);

        try {
            mostrarAnimacion1();
            const response  =   await fetch(urlShowProyecto, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                    });

            const   res =   await response.json();
                                
            if(res.success){

                pintarProyecto(res.proyecto);

                limpiarTabla('table_show_proyecto_personal');
                destruirDataTable(dtShowProyectoPersonal);
                pintarTableShowProyectoPersonal(res.personal);
                iniciarDataTableShowProyectoPersonal();

                limpiarTabla('table_show_proyecto_maquinaria');
                destruirDataTable(dtShowProyectoMaquinaria);
                pintarTableShowProyectoMaquinaria(res.maquinaria);
                iniciarDataTableShowProyectoMaquinaria();
                toastr.success(res.message,'OPERACIÓN COMPLETADA');
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR');
            }

              
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VER PRODUCTO');
        }finally{
            ocultarAnimacion1();
        } 
    }

   
    function pintarProyecto(proyecto){
        document.querySelector('#spanNombre').textContent        =   proyecto.nombre;
        document.querySelector('#spanCosto').textContent         =   proyecto.costo;
        document.querySelector('#spanAvanceCosto').textContent   =   proyecto.avance_costo;
        document.querySelector('#spanDiferencia').textContent    =   proyecto.diferencia;
        document.querySelector('#spanSupervisor').textContent    =   proyecto.supervisor_nombre?proyecto.supervisor_nombre:'SIN SUPERVISOR';


    }

    function pintarTableShowProyectoPersonal(personal){
        let filas   =   ``;
        const tbody =   document.querySelector('#table_show_proyecto_personal tbody');
        personal.forEach((p)=>{
            filas   +=  `<tr>
                            <th>${p.nombre}</th>
                            <td>${p.tipo_documento_descripcion}</td>
                            <td>${p.nro_documento}</td>
                        </tr>`;
        })
        tbody.innerHTML =   filas;
    }

    function pintarTableShowProyectoMaquinaria(maquinaria){
        let filas   =   ``;
        const tbody =   document.querySelector('#table_show_proyecto_maquinaria tbody');
        maquinaria.forEach((m)=>{
            filas   +=  `<tr>
                            <th>${m.nombre}</th>
                            <td>${m.tipo_gasto_descripcion}</td>
                            <td>${m.costo_gasto}</td>
                            <td>${m.observacion ? m.observacion:''}</td>
                        </tr>`;
        })
        tbody.innerHTML =   filas;
    }

    function limpiarModalShow(){
        document.querySelector('#spanNombre').textContent           =   '';
        document.querySelector('#spanCosto').textContent            =   '';
        document.querySelector('#spanAvanceCosto').textContent      =   '';
        document.querySelector('#spanDiferencia').textContent       =   '';
        document.querySelector('#spanUnidad').textContent           =   '';

        limpiarTabla('table_show_proyecto_personal');
        destruirDataTable(dtShowProyectoPersonal);
        iniciarDataTableShowProyectoPersonal();

        limpiarTabla('table_show_proyecto_maquinaria');
        destruirDataTable(dtShowProyectoMaquinaria);
        iniciarDataTableShowProyectoMaquinaria();
    }
</script>