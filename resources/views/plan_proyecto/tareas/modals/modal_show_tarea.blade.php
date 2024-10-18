<div class="modal fade" id="mdlShowTarea" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <!-- Encabezado de la tarjeta -->
          <div class="modal-header text-light">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Ver Tarea</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <!-- Cuerpo de la tarjeta -->
          <div class="modal-body">
              <div class="row">
                  <!-- Sección de Información del Producto -->
                  <div class="col-12">
                      <div class="border rounded p-4 bg-white shadow-sm">
                          <h5 class="text-primary border-bottom pb-2 mb-3">Detalles de la Tarea</h5>
                          
                          <!-- Mostrar los datos -->
                          <div class="row mb-3">
                              <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                  <strong class="text-primary">Nombre:</strong>
                                  <span class="text-muted" id="spanNombreShow"></span>
                              </div>
                              <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                  <strong class="text-primary">Fecha Inicio:</strong>
                                  <span class="text-muted" id="spanFechaInicioShow"></span>
                              </div>
                              <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                  <strong class="text-primary">Fecha Fin:</strong>
                                  <span class="text-muted" id="spanFechaFinShow"></span>
                              </div>
                              <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                  <strong class="text-primary">Avance:</strong>
                                  <span class="text-muted" id="spanAvanceShow"></span>
                              </div>
                              <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                  <strong class="text-primary">Días faltantes:</strong>
                                  <span class="text-muted" id="spanDiasFaltantesShow"></span>
                              </div>
                              <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                  <strong class="text-primary">Observación:</strong>
                                  <span class="text-muted" id="spanObservacionShow"></span>
                              </div>
                              <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">                                    
                                <strong class="text-primary">PROYECTO:</strong>
                                <span class="text-muted" id="spanProyectoNombreShow"></span>
                            </div>
                          </div>
                      </div>
                  </div>
              </div>

              <!-- Tabla de Stocks (se puede agregar después) -->
              <div class="row mt-4">
                  <div class="col-12">
                      <div class="border rounded p-4 bg-white shadow-sm">
                          <h6 class="text-primary mb-3 border-bottom pb-2">Subtareas</h6>
                          <div class="table-responsive">
                              @include('plan_proyecto.tareas.tables.table_show_subtareas') 
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

    let dtShowSubtareas =   null;

    async function openMdlShowTarea(tarea_id){
      await getShowTarea(tarea_id);
      $('#mdlShowTarea').modal('show');
    }

    async function getShowTarea(tarea_id){
        toastr.clear();
        const token                 =   document.querySelector('input[name="_token"]').value;
        const urlShowTarea          =   `{{ route('plan_proyecto.tarea.show', ':id') }}`.replace(':id', tarea_id);

        try {
            mostrarAnimacion1();
            const response  =   await fetch(urlShowTarea, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                    });

            const   res =   await response.json();
                                
            if(res.success){
                pintarTareaShow(res.tarea);

                limpiarTabla('table_show_subtareas');
                destruirDataTable(dtShowSubtareas);
                pintarTableShowSubtareas(res.subtareas);
                iniciarDataTableShowSubtareas();

               
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

    function pintarTableShowSubtareas(subtareas){
      let filas   =   ``;
        const tbody =   document.querySelector('#table_show_subtareas tbody');
        subtareas.forEach((s)=>{
            filas   +=  `<tr>
                            <th>${s.nombre}</th>
                            <td>${s.fecha_inicio}</td>
                            <td>${s.fecha_fin}</td>
                            <td>${s.observacion?s.observacion:''}</td>
                        </tr>`;
        })
        tbody.innerHTML =   filas;
    }

    function iniciarDataTableShowSubtareas(){
        dtShowSubtareas  =   new DataTable('#table_show_subtareas',{
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

    function pintarTareaShow(tarea){
        
      const spanNombreShow          = document.querySelector('#spanNombreShow');
      const spanFechaInicioShow     = document.querySelector('#spanFechaInicioShow');
      const spanFechaFinShow        = document.querySelector('#spanFechaFinShow');
      const spanAvanceShow          = document.querySelector('#spanAvanceShow');
      const spanDiasFaltantesShow   = document.querySelector('#spanDiasFaltantesShow');
      const spanObservacionShow     = document.querySelector('#spanObservacionShow');
      const spanProyectoNombreShow  = document.querySelector('#spanProyectoNombreShow');


      spanNombreShow.textContent          = tarea.nombre;
      spanFechaInicioShow.textContent     = tarea.fecha_inicio;
      spanFechaFinShow.textContent        = tarea.fecha_fin;
      spanAvanceShow.textContent          = tarea.avance;
      spanDiasFaltantesShow.textContent   = tarea.dias_faltantes;
      spanObservacionShow.textContent     = tarea.observacion?tarea.observacion:'';
      spanProyectoNombreShow.textContent  = tarea.proyecto_nombre;
    }
</script>