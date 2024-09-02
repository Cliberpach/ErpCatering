
@extends('layouts.layout')
@section('title-page')
    LISTADO DE ASISTENCIAS
@endsection

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Registro de labor <i class="fa-solid fa-clipboard-user"></i>
      </h6>
        @role('SUPERVISOR')
            <button class="btn btn-primary" onclick="registrarMaestroAsistencia()">
                <i class="fa-solid fa-plus"></i> NUEVO
            </button>
        @endrole
    </div>
    <div class="table-responsive">
        @include('jornales.registro_labor.tables.table_maestro_asistencia')
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
    let dtRegistrosLabor    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableRegistrosLabor();
    })

    function iniciarDataTableRegistrosLabor(){
        const urlGetRegistrosLabor = '{{ route('jornales.registro_labor.getRegistrosLabor') }}';

        dtRegistrosLabor  =   new DataTable('#table_maestro_asistencia',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetRegistrosLabor,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'supervisor_nombre', name: 'supervisor_nombre' },
                { data: 'fecha_registro', name: 'fecha_registro' },
                { data: 'cant_trabajadores', name: 'cant_trabajadores' },
                { data: 'observacion', name: 'observacion' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('registros.proyecto.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete = `{{ route('registros.colaborador.destroy', ':id') }}`.replace(':id', data.id);

                        return `
                            <div class="btn-group">
                            <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 100px; overflow-y: auto;">
                                <li>
                                    <a class="dropdown-item" href="${urlEdit}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarProyecto(${data.id})">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </a>
                                </li>
                            </ul>
                            </div>
                        `;
                    },
                    name: 'actions', 
                    orderable: false, 
                    searchable: false 
                }
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


    function registrarMaestroAsistencia(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA INICIAR ASISTENCIA DEL DÍA?",
        text: "Se iniciará el control de asistencia de su proyecto en el día!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            
            Swal.fire({
                title: 'Cargando...',
                html: 'Iniciando asistencia ...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlIniciarAsistencia    =   @json(route('jornales.registro_labor.store'));
                const token                 =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlIniciarAsistencia, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtRegistrosLabor.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL INICIAR ASISTENCIA');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN INICIAR ASISTENCIA');
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


    function eliminarProyecto(id){
        toastr.clear();
        let row             =   getRowById(dtRegistrosLabor,id);
        let message         =   '';
        let tipo_documento  =   '';

        message =   `Desea eliminar el proyecto: ${row.nombre}`;

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: message,
        text: "Operación no reversible!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            
            Swal.fire({
                title: 'Cargando...',
                html: 'Eliminando producto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlIniciarAsistencia    =   `{{ route('registros.proyecto.destroy', ['id' => ':id']) }}`;
                urlIniciarAsistencia        =   urlIniciarAsistencia.replace(':id', id);
                const token              =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlIniciarAsistencia, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtRegistrosLabor.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR PROYECTO');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR PROYECTO');
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