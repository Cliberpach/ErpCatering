@extends('layouts.layout')
@section('title-page')
    LISTADO DE MOTIVOS DE DESCANSO
@endsection

@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('motivo_descanso-active', 'active')


@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Motivos de Descanso <i class="fa-solid fa-toolbox"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearMotivo()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="table-responsive">
        @include('registros.motivo_descanso.tables.table_list_motivo_descanso')
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
    let dtMotivo_Descanso    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTable();
    })

    function iniciarDataTable(){
        const urlGetMotivo = '{{ route('registros.motivo_descanso.getMotivoDescanso') }}';

        dtMotivo_Descanso  =   new DataTable('#table_motivo',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetMotivo,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'descripcion', name: 'descripcion' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('registros.motivo_descanso.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete = `{{ route('registros.motivo_descanso.destroy', ':id') }}`.replace(':id', data.id);

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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarMotivo(${data.id})">
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

    function goToCrearMotivo(){
        window.location.href = @json(route('registros.motivo_descanso.create'));
    }


    function eliminarMotivo(id){
        toastr.clear();
        let row             =   getRowById(dtMotivo_Descanso,id);
        let message         =   '';

        message =   `Desea eliminar el Motivo de Descanso: ${row.id}`;

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
                html: 'Eliminando motivo de descanso...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            
            try {
                let urlDeleteMotivo     =   `{{ route('registros.motivo_descanso.destroy', ['id' => ':id']) }}`;
                urlDeleteMotivo         =   urlDeleteMotivo.replace(':id', id);
                const token                 =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteMotivo, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtMotivo_Descanso.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR EL MOTIVO DE DESCANSO');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR EL MOTIVO DE DESCANSO');
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