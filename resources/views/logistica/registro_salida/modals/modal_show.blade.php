<div class="modal fade" id="mdlShow" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: white; border: 1px solid #007bff;">
            <div class="modal-header" style="background-color: #007bff; color: white;">
                <h1 class="modal-title fs-5" id="exampleModalLabel" style="color:white;"><i class="fas fa-box"></i> Detalles de la Salida</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div style="padding: 15px; border-radius: 10px; border: 1px solid #007bff; background-color: #f0f8ff;">
                            <h5 style="margin: 0; color: #007bff;"><i class="fas fa-id-badge"></i> ID: <p style="margin:0;" id="id_show"></p></h5>
                            <p style="margin: 0;"><i class="fas fa-user"></i> <strong>Colaborador:</strong><p style="margin:0;" id="colaborador_show"></p></p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div style="padding: 15px; border-radius: 10px; border: 1px solid #007bff; background-color: #f0f8ff;">
                            <p style="margin: 0;"><i class="fas fa-arrow-right"></i> <strong>Almacén Origen:</strong><p style="margin:0;" id="almacen_origen_show"></p></p>
                            <p style="margin: 0;"><i class="fas fa-arrow-left"></i> <strong>Almacén Destino:</strong><p style="margin:0;" id="almacen_destino_show"></p></p>
                            <p style="margin: 0;"><i class="fas fa-calendar-alt"></i> <strong>Fecha de Registro:</strong><p style="margin:0;" id="fecha_show"></p></p>
                        </div>
                    </div>
                </div>
                <hr style="border-color: #007bff;">

                @include('logistica.registro_salida.tables.table_show')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>



<script>
    let dtShow  =   null;

    async function openMdlShow(salida_id){
        await getShowSalida(salida_id);
        $('#mdlShow').modal('show');
    }

    async function getShowSalida(salida_id){
        toastr.clear();
        const token                 =   document.querySelector('input[name="_token"]').value;
        const urlShowSalida         =   `{{ route('logistica.registro_salida.show', ':id') }}`.replace(':id', salida_id);

        try {
            mostrarAnimacion1();
            const response  =   await fetch(urlShowSalida, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                    });

            const   res =   await response.json();
                                
            if(res.success){
                pintarSalida(res.registro_salida);
                limpiarTabla('table_show');
                destruirDataTable(dtShow);
                pintarTableShow(res.registro_salida_detalle);
                iniciarDataTableShow();
                toastr.success(res.message,'OPERACIÓN COMPLETADA');
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR');
            }

              
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VER SALIDA');
        }finally{
            ocultarAnimacion1();
        }
          
    }

    function iniciarDataTableShow(){
        dtShow  =   new DataTable('#table_show',{
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

    function pintarSalida(registro_salida){
        document.querySelector('#id_show').textContent                  =   registro_salida.id;
        document.querySelector('#colaborador_show').textContent         =   registro_salida.colaborador_nombre;
        document.querySelector('#almacen_origen_show').textContent      =   registro_salida.almacen_origen_nombre;
        document.querySelector('#almacen_destino_show').textContent     =   registro_salida.almacen_destino_nombre;
        document.querySelector('#fecha_show').textContent               =   registro_salida.fecha_registro;
    }

    function pintarTableShow(registro_salida_detalle){
        let filas   =   ``;
        const tbody =   document.querySelector('#table_show tbody');
        registro_salida_detalle.forEach((rsd)=>{
            filas   +=  `<tr>
                            <th>${rsd.cantidad}</th>
                            <td>${rsd.producto_nombre}</td>
                            <td>${rsd.categoria_nombre}</td>
                            <td>${rsd.marca_nombre}</td>
                        `;
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