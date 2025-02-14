@extends('layouts.layout')
@section('title-page')
    LISTADO DE HORARIOS
@endsection

@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('horario-active', 'active')


@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>HORARIO <i class="fa-solid fa-toolbox"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearHorario()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="table-responsive">
        @include('registros.horario.tables.table_list_horario')
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
    let dtHorario    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTable();
    })

    function iniciarDataTable(){
        const urlGetHorario = '{{ route('registros.horario.getHorario') }}';

        dtHorario  =   new DataTable('#table_horario',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetHorario,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre_proyecto', name: 'nombre_proyecto' },
                { data: 'hora_inicio', name: 'hora_inicio' },
                { data: 'hora_final', name: 'hora_final' },
                { data: 'descripcion', name: 'descripcion' },
                { data: 'minutos_tolerancia', name: 'minutos_tolerancia' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('registros.horario.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete = `{{ route('registros.horario.destroy', ':id') }}`.replace(':id', data.id);

                        return `
                            <div class="btn-group dropstart">
                            <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 150px; overflow-y: auto;">
                                <li>
                                    <a class="dropdown-item" href="${urlEdit}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarHorario(${data.id})">
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

    function goToCrearHorario(){
        window.location.href = @json(route('registros.horario.create'));
    }


    function eliminarHorario(id){
        toastr.clear();
        let row             =   getRowById(dtHorario,id);
        let message         =   '';

        message =   `Desea eliminar el Horario: ${row.id}`;

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
                html: 'Eliminando Horario ...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            
            try {
                let urlDeleteHorario     =   `{{ route('registros.horario.destroy', ['id' => ':id']) }}`;
                urlDeleteHorario         =   urlDeleteHorario.replace(':id', id);
                const token                 =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteHorario, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtHorario.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR EL Horario');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR EL Horario');
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