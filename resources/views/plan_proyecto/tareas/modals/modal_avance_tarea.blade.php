<div class="modal fade" id="mdlAvance" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Encabezado de la tarjeta -->
            <div class="modal-header text-light">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Avance</h1>
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
                                    <span class="text-muted" id="spanNombreAvance"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">Fecha Inicio:</strong>
                                    <span class="text-muted" id="spanFechaInicioAvance"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">Fecha Fin:</strong>
                                    <span class="text-muted" id="spanFechaFinAvance"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">Avance:</strong>
                                    <span class="text-muted" id="spanAvanceAvance"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">Días faltantes:</strong>
                                    <span class="text-muted" id="spanDiasFaltantesAvance"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">Observación:</strong>
                                    <span class="text-muted" id="spanObservacionAvance"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">                                    
                                  <strong class="text-primary">PROYECTO:</strong>
                                  <span class="text-muted" id="spanProyectoNombreAvance"></span>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
  
                <!-- Tabla de Stocks (se puede agregar después) -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="border rounded bg-white shadow-sm">
                            <h6 class="text-primary mb-3 border-bottom p-2">Subtareas</h6>
                            <div class="table-responsive">
                                @include('plan_proyecto.tareas.tables.table_avance_tarea') 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
  
            <!-- Pie de la tarjeta -->
            <div class="modal-footer text-white d-flex justify-content-end">
                <button type="button" class="btn btn-primary" onclick="registrarAvance();">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
  </div>
<script>
  
      let dtAvanceTarea                 =   null;
      const lstSubtareasAvance          =   [];
      const parametrosMdlAvanceTarea    =   {tarea_id:null};

      function eventsMdlAvanceTarea(){
        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('chkAvanceTarea')){

                const tarea_index       =   e.target.getAttribute('data-id');
                const marcado           =   e.target.checked;
                const subtarea_buscada  =   lstSubtareasAvance[tarea_index];

                if(marcado){
                    subtarea_buscada.estado =   'FINALIZADO';
                }else{
                    subtarea_buscada.estado =   'PENDIENTE';
                }

                const currentPage = dtAvanceTarea.page.info().page;
                limpiarTabla('table_avance_tarea');
                destruirDataTable(dtAvanceTarea);
                pintarTableAvanceTarea(lstSubtareasAvance);
                iniciarDataTableAvanceTarea();
                dtAvanceTarea.page(currentPage).draw(false);
            }
        })
      }
  
      async function openMdlAvance(tarea_id){
        parametrosMdlAvanceTarea.tarea_id   =   tarea_id;
        await getTareaAvance(tarea_id);
        $('#mdlAvance').modal('show');
      }
  
      async function getTareaAvance(tarea_id){
          toastr.clear();
          const token                   =   document.querySelector('input[name="_token"]').value;
          const urlShowTarea            =   `{{ route('plan_proyecto.tarea.show', ':id') }}`.replace(':id', tarea_id);
          lstSubtareasAvance.length     =   0;

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
  
                  pintarTarea(res.tarea);
  
                  res.subtareas.forEach((s)=>{
                    lstSubtareasAvance.push(s);
                  })

                  limpiarTabla('table_avance_tarea');
                  destruirDataTable(dtAvanceTarea);
                  pintarTableAvanceTarea(lstSubtareasAvance);
                  iniciarDataTableAvanceTarea();
  
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
  
      function pintarTableAvanceTarea(subtareas){
        let filas   =   ``;
          const tbody =   document.querySelector('#table_avance_tarea tbody');
          subtareas.forEach((s,index)=>{
                let estado      =   ``;
                let chkEstado   =   '';

                if(s.estado === 'PENDIENTE'){
                    estado      =   `<span class="badge bg-danger">${s.estado}</span>`;
                    chkEstado   =   `<input data-id="${index}" type="checkbox" class="form-check-input chkAvanceTarea" id="exampleCheck1">`;
                }

                if(s.estado === 'FINALIZADO'){
                    estado      =   `<span class="badge bg-primary">${s.estado}</span>`;
                    chkEstado   =   `<input checked data-id="${index}" type="checkbox" class="form-check-input chkAvanceTarea" id="exampleCheck1">`;
                }

                filas   +=  `<tr>
                                    <th>${chkEstado}</th>
                                    <th>
                                        <div style="min-width:200px;">
                                            <p style="margin:0;padding:0;">${s.nombre}</p>    
                                        </div>    
                                    </th>
                                    <td>
                                        <div style="min-width:140px;">
                                            ${s.fecha_inicio}
                                        </div>  
                                    </td>
                                    <td>
                                        <div style="min-width:140px;">
                                            ${s.fecha_fin}
                                        </div>  
                                    </td>
                                     <td>
                                        <div style="min-width:100px;">
                                            ${estado}
                                        </div>    
                                    </td>
                                    <td>${s.observacion?s.observacion:''}</td>
                            </tr>`;
          })
          tbody.innerHTML =   filas;
      }
  
      function iniciarDataTableAvanceTarea(){
          dtAvanceTarea  =   new DataTable('#table_avance_tarea',{
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
  
      function pintarTarea(tarea){
        const spanNombreAvance          = document.querySelector('#spanNombreAvance');
        const spanFechaInicioAvance     = document.querySelector('#spanFechaInicioAvance');
        const spanFechaFinAvance        = document.querySelector('#spanFechaFinAvance');
        const spanAvanceAvance          = document.querySelector('#spanAvanceAvance');
        const spanDiasFaltantesAvance   = document.querySelector('#spanDiasFaltantesAvance');
        const spanObservacionAvance     = document.querySelector('#spanObservacionAvance');
        const spanProyectoNombreAvance  = document.querySelector('#spanProyectoNombreAvance');
  
  
        spanNombreAvance.textContent          = tarea.nombre;
        spanFechaInicioAvance.textContent     = tarea.fecha_inicio;
        spanFechaFinAvance.textContent        = tarea.fecha_fin;
        spanAvanceAvance.textContent          = tarea.avance;
        spanDiasFaltantesAvance.textContent   = tarea.dias_faltantes;
        spanObservacionAvance.textContent     = tarea.observacion?tarea.observacion:'';
        spanProyectoNombreAvance.textContent  = tarea.proyecto_nombre;
      }

      function registrarAvance(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "Desea registrar el avance?",
        text: "Se modificará el progeso de la tarea!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, registrar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
          
            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando avance de la tarea...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {

                const token                     =   document.querySelector('input[name="_token"]').value;
                let urlAvanceTarea              =   `{{ route('plan_proyecto.tarea.avance', ['id' => ':id']) }}`;
                urlAvanceTarea                  =   urlAvanceTarea.replace(':id', parametrosMdlAvanceTarea.tarea_id);   
                const formData                  =   new FormData();
                formData.append('lstSubtareasAvance', JSON.stringify(lstSubtareasAvance));
               
                const response = await fetch(urlAvanceTarea, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': token,
                                        'X-HTTP-Method-Override': 'PUT' 
                                    },
                                    body: formData  
                                });

                const   res =   await response.json();


                if(response.status === 422){
                    if('errors' in res){
                        pintarErroresValidacion(res.errors);
                    }
                    Swal.close();
                    return;
                }

                if(res.success){
                    dtTareas.ajax.reload(null, false);
                    $('#mdlAvance').modal('hide');
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                }


                } catch (error) {
                    toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR TAREA');
                }finally{
                    Swal.close();
                }

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire({
            title: "Operación cancelada",
            text: "No se realizaron cambios",
            icon: "error"
            });
        }
        });
      }

  </script>