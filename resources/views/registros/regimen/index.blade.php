@extends('layouts.layout')
@section('title-page')
    LISTADO DE REGIMENES
@endsection

@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('regimen-active', 'active')


@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Regimen <i class="fa-solid fa-toolbox"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearRegimen()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="table-responsive">
        @include('registros.regimen.tables.table_list_regimen')
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
    let dtRegimen    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTable();
    })

    function iniciarDataTable(){
        const urlGetRegimen = '{{ route('registros.regimen.getRegimen') }}';

        dtRegimen  =   new DataTable('#table_regimen',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetRegimen,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'descripcion', name: 'descripcion' },
                { data: 'dias_trabajo', name: 'dias_trabajo' },
                { data: 'dias_descanso', name: 'dias_descanso' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('registros.regimen.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete = `{{ route('registros.regimen.destroy', ':id') }}`.replace(':id', data.id);

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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarRegimen(${data.id})">
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

    function goToCrearRegimen(){
        window.location.href = @json(route('registros.regimen.create'));
    }


    function eliminarRegimen(id){
        toastr.clear();
        let row             =   getRowById(dtRegimen,id);
        let message         =   '';

        message =   `Desea eliminar el regimen: ${row.nombre}`;

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
                html: 'Eliminando regimen...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteRegimen     =   `{{ route('registros.regimen.destroy', ['id' => ':id']) }}`;
                urlDeleteRegimen         =   urlDeleteRegimen.replace(':id', id);
                const token                 =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteRegimen, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtRegimen.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR REGIMEN');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR REGIMEN');
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