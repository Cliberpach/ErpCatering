@extends('layouts.layout')
@section('title-page')
    LISTADO DE CONDUCTORES
@endsection

@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('conductor-active', 'active')


@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Conductores <i class="fa-solid fa-user"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearConductor()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="table-responsive">
        @include('registros.conductores.tables.table_list_conductores')
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
    let dtConductores    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableConductores();
    })

    function iniciarDataTableConductores(){
        const urlGetConductores = '{{ route('registros.conductor.getConductores') }}';

        dtConductores  =   new DataTable('#table_list_conductores',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetConductores,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'tipo_documento', name: 'tipo_documento' },
                { data: 'nro_documento', name: 'nro_documento' },
                { data: 'telefono', name: 'telefono' },
                { data: 'licencia', name: 'licencia' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('registros.conductor.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete = `{{ route('registros.colaborador.destroy', ':id') }}`.replace(':id', data.id);

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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarConductor(${data.id})">
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

    function goToCrearConductor(){
        window.location.href = @json(route('registros.conductor.create'));
    }


    function eliminarConductor(id){
        toastr.clear();
        let row             =   getRowById(dtConductores,id);
        let message         =   '';
        let tipo_documento  =   '';

        if(row.tipo_documento_id == 1){
            tipo_documento  =   'DNI';
        }

        if(row.tipo_documento_id == 2){
            tipo_documento  =   'CARNET EXTRANJERÍA';
        }


        message =   `Desea eliminar el conductor: ${row.nombre}, ${tipo_documento}:${row.nro_documento}`;

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
                html: 'Eliminando conductor...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteConductor    =   `{{ route('registros.conductor.destroy', ['id' => ':id']) }}`;
                urlDeleteConductor        =   urlDeleteConductor.replace(':id', id);
                const token               =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteConductor, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtConductores.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR CONDUCTOR');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR CONDUCTOR');
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