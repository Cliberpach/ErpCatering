
@extends('layouts.layout')
@section('title-page')
    LISTADO DE REGISTROS DE TAREA
@endsection

@section('trabajo_equipos-collapsed', '')
@section('trabajo_equipos-expanded', 'true')
@section('trabajo_equipos-show', 'show')
@section('registro_tarea-active', 'active')

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Viajes de Maquinaria <i class="fa-solid fa-clipboard-user"></i>
      </h6>
        @role('SUPERVISOR')
            <button class="btn btn-primary" onclick="goToRegistroTareaCreate()">
                <i class="fa-solid fa-plus"></i> NUEVO
            </button>
        @endrole
    </div>
    <div class="table-responsive">
        @include('trabajo_equipos.registro_tarea.tables.table_list_registro_tarea')
    </div>
</div>
<!-- end card -->
@endsection

@if(Session::has('message_success'))
<script>
    var message = "{{ Session::get('message_success') }}";
    toastr.success(message, 'OPERACIÓN COMPLETADA');
</script>
@endif

<script>
    let dtRegistrosTarea    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableRegistrosTarea();
    })

    function iniciarDataTableRegistrosTarea(){
        const urlGetRegistrosTarea = '{{ route('trabajo_equipos.registro_tarea.getRegistrosTarea') }}';

        dtRegistrosTarea  =   new DataTable('#table_list_registro_tarea',{
            serverSide: true,
            processing: true,
            responsive:true,
            pageLength: 50, 

            ajax: {
                url: urlGetRegistrosTarea,
                type: 'GET',
            },
            columnDefs: [
                {
                    targets: 0,           
                    visible: false,       
                }
            ],
            columns: [
                { data: 'id', name: 'id' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('trabajo_equipos.registro_tarea.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete     = `{{ route('trabajo_equipos.registro_tarea.destroy', ['id' => ':id']) }}`.replace(':id', data.id);

                        let acciones        =   ``;
                        if(@json(Auth::user()->colaborador_id) == row.supervisor_id){
                            acciones    =   `<div class="btn-group">
                                                <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-grip"></i>
                                                </button>
                                                <ul class="dropdown-menu" style="max-height: 100px; overflow-y: auto;">
                                                    <li>
                                                        <a class="dropdown-item" href="${urlEdit}">
                                                            <i class="fa-solid fa-file-pen"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarRegistroTarea(${data.id})">
                                                            <i class="fa-solid fa-trash"></i> Eliminar
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>`;
                        }
                        return acciones;
                    },
                    name: 'actions', 
                    orderable: false, 
                    searchable: false 
                },
                { data: 'proyecto_nombre', name: 'proyecto_nombre' },
                { data: 'supervisor_nombre', name: 'supervisor_nombre' },
                { data: 'maquinaria_nombre', name: 'maquinaria_nombre' },
                { data: 'observacion', name: 'observacion' },
                { data: 'cantidad_horas_viajes', name: 'cantidad_horas_viajes' },
                { data: 'fecha_registro', name: 'fecha_registro' },
            ],
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


    function goToRegistroTareaCreate(){
        window.location.href = @json(route('trabajo_equipos.registro_tarea.create'));
    }


    function eliminarRegistroTarea(id){
        toastr.clear();
        let row             =   getRowById(dtRegistrosTarea,id);
        let message         =   '';
        let tipo_documento  =   '';

        message =   `Desea eliminar la tarea de la maquinaria ${row.maquinaria_nombre}`;

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: message,
        text: `Fecha: ${row.fecha_registro} - Proyecto: ${row.proyecto_nombre}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            
            Swal.fire({
                title: 'Cargando...',
                html: 'Eliminando tarea...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlEliminarTarea    =   `{{ route('trabajo_equipos.registro_tarea.destroy', ['id' => ':id']) }}`;
                urlEliminarTarea        =   urlEliminarTarea.replace(':id', id);
                const token             =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlEliminarTarea, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtRegistrosTarea.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR TAREA');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR TAREA');
            }finally{
                Swal.close();
            }

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire({
            title: "Operación cancelada",
            text: "No se realizaron acciones",
            icon: "error"
            });
        }
        });
    }


</script>