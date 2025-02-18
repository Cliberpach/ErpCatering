@extends('layouts.layout')
@section('title-page')
    LISTADO DE SEDES-PERSONAL
@endsection

@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('proyectos-active', 'active')


@section('section-page')
@include('registros.proyectos.modals.modal_asignar_supervisor')
@include('registros.proyectos.modals.modal_show')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
    </div>

    <div class="row justify-content-between mb-3">
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
            <select id="selectProyecto" class="form-control select2_form" onchange="cambiarProyecto(this.value)">
                @foreach($proyectos as $proyecto)
                    <option value="{{ $proyecto->id }}" 
                        {{ isset($primerProyecto) && $primerProyecto->id == $proyecto->id ? 'selected' : '' }}>
                        {{ $proyecto->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6" style="display: flex;justify-content:end;">
            <button class="btn btn-primary" onclick="goToCrearProyecto()">
                <i class="fa-solid fa-plus"></i> Crear Proyecto
            </button>
        </div>

      
    </div>

    <div class="table-responsive">
        @include('registros.proyectos.tables.table_list_proyectos_resumen')
        @include('registros.proyectos.tables.table_list_colaborador_regimen')

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
    let dtProyectosDireccion    =   null;
    let dtColaboradores         =   null;

    document.addEventListener('DOMContentLoaded', () => {
        iniciarDataTableProyectos();
        iniciarDataTableColaboradores();
        iniciarSelect2();
        eventsMdlAsignarSupervisor();
        eventsMdlShowProyecto();

            

    });



function iniciarDataTableProyectos() {
    const urlGetProyectosDireccion = '{{ route("registros.proyecto.getProyectosDireccion") }}';

    dtProyectosDireccion = new DataTable('#table_proyectos_resumen', {
        serverSide: true,
        processing: true,
        ajax: {
            url: urlGetProyectosDireccion,
            type: 'GET',
            data: function (d) {
                d.proyecto_id  =   $('#selectProyecto').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'nombre', name: 'nombre' },
            { data: 'supervisor_nombre', name: 'supervisor_nombre' },
            { data: 'direccion', name: 'direccion' },
            {
                data: null, 
                render: function(data, type, row) {
                    const baseUrlEdit = `{{ route('registros.proyecto.edit', ['id' => ':id']) }}`;
                    const urlEdit = baseUrlEdit.replace(':id', data.id); 

                    const baseUrlAsignarPersonal = `{{ route('registros.proyecto.asignarPersonalCreate', ['id' => ':id']) }}`;   
                    const urlAsignarPersonal = baseUrlAsignarPersonal.replace(':id', data.id);

                    const baseUrlAsignarMaquinaria = `{{ route('registros.proyecto.asignarMaquinariaCreate', ['id' => ':id']) }}`;   
                    const urlAsignarMaquinaria = baseUrlAsignarMaquinaria.replace(':id', data.id);

                    const urlDelete = `{{ route('registros.colaborador.destroy', ':id') }}`.replace(':id', data.id);

                    return `
                        <div class="btn-group dropstart">
                            <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 150px; overflow-y: auto;">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlShowProyecto(${data.id})">
                                        <i class="fa-solid fa-eye"></i> Ver
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="${urlEdit}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarProyecto(${data.id})">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="finalizarProyecto(${data.id})">
                                        <i class="fa-solid fa-flag-checkered"></i> Finalizar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlAsignarSupervisor(${data.id})">
                                        <i class="fa-solid fa-book-open-reader"></i> Asignar supervisor
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="${urlAsignarPersonal}">
                                        <i class="fa-solid fa-people-group"></i> Asignar personal
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="${urlAsignarMaquinaria}">
                                        <i class="fa-solid fa-tractor"></i> Asignar maquinaria
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
    console.log('DataTable inicializado correctamente.'); // Depuración

}
    function goToCrearProyecto(){
        window.location.href = "{{ route('registros.proyecto.create') }}";
}

function iniciarDataTableColaboradores(proyecto_id = null) {
    if (dtColaboradores) {
        dtColaboradores.destroy();
    }
    const urlGetProyectosRegimen = '{{ route("registros.proyecto.getColaboradoresRegimen") }}';

    dtColaboradores = new DataTable('#table_colaboradores_regimen', {
        serverSide: true,
        processing: true,
        ajax: {
            url: urlGetProyectosRegimen,
            type: 'GET',
            data: function (d) {
                d.proyecto_id  =   $('#selectProyecto').val();
            }
        },
        columns: [
            { data: 'nombre', name: 'nombre' },
            { data: 'dni', name: 'dni' },
            { data: 'horario', name: 'horario', defaultContent: '<span class="text-muted">No asignado</span>' },
            { data: 'regimen', name: 'regimen', defaultContent: '<span class="text-muted">No asignado</span>' },
            { 
                data: null, 
                render: function(data) {
                    return `
                        <button class="btn btn-primary" onclick="asignarHorarioRegimen(${data.id})">
                            <i class="fa-solid fa-clock"></i> Asignar
                        </button>`;
                }, 
                orderable: false, 
                searchable: false 
            }
        ],
        language: {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "search": "Buscar:",
            "paginate": {
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });
}

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            allowClear: true,
        } );
    }

    function eliminarProyecto(id){
        toastr.clear();
        let row             =   getRowById(dtProyectosDireccion,id);
        let message         =   '';

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
                html: 'Eliminando proyecto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteProyecto    =   `{{ route('registros.proyecto.destroy', ['id' => ':id']) }}`;
                urlDeleteProyecto        =   urlDeleteProyecto.replace(':id', id);
                const token              =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteProyecto, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtProyectosDireccion.draw();
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




    function finalizarProyecto(id){
        toastr.clear();
        let row             =   getRowById(dtProyectosDireccion,id);
        let message         =   '';
      

        message =   `Desea finalizar el proyecto: ${row.nombre}`;

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
        confirmButtonText: "Sí, finalizar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            
            Swal.fire({
                title: 'Cargando...',
                html: 'Finalizando proyecto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlFinalizarProyecto    =   `{{ route('registros.proyecto.finalizarProyecto', ['id' => ':id']) }}`;
                urlFinalizarProyecto        =   urlFinalizarProyecto.replace(':id', id);
                const token                 =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlFinalizarProyecto, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                            'X-HTTP-Method-Override': 'PATCH' 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtProyectosDireccion.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL FINALIZAR PROYECTO');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN FINALIZAR PROYECTO');
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

    function cambiarProyecto(proyecto_id){
        console.log(proyecto_id);
    }


</script>