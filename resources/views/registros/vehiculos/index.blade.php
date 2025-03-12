@extends('layouts.layout')
@section('title-page')
    LISTADO DE VEHICULOS
@endsection

@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('vehiculo-active', 'active')


@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Vehículos <i class="fa-solid fa-toolbox"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearVehiculo()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="table-responsive">
        @include('registros.vehiculos.tables.table_list_vehiculos')
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
    let dtVehiculos    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableVehiculos();
    })

    function iniciarDataTableVehiculos(){
        const urlGetVehiculos = '{{ route('registros.vehiculo.getVehiculos') }}';

        dtVehiculos  =   new DataTable('#table_list_vehiculos',{
            serverSide: true,
            processing: true,
            pageLength: 50, 

            ajax: {
                url: urlGetVehiculos,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'placa', name: 'placa' },
                { data: 'modelo', name: 'tipo_gamodelosto_nombre' },
                { data: 'marca', name: 'marca' },
                { data: 'fecha_registro', name: 'fecha_registro' },
                { data: 'fecha_modificacion', name: 'fecha_modificacion' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const urlEdit   =   `{{ route('registros.vehiculo.edit', ['id' => ':id']) }}`.replace(':id', data.id);

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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarVehiculo(${data.id})">
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

    function goToCrearVehiculo(){
        window.location.href = @json(route('registros.vehiculo.create'));
    }


    function eliminarVehiculo(id){
        toastr.clear();
        let row             =   getRowById(dtVehiculos,id);
        let message         =   '';

        message =   `Desea eliminar el vehiculo: ${row.placa}`;

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
                html: 'Eliminando vehículo...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteVehiculo     =   `{{ route('registros.vehiculo.destroy', ['id' => ':id']) }}`;
                urlDeleteVehiculo         =   urlDeleteVehiculo.replace(':id', id);
                const token               =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteVehiculo, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtVehiculos.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR VEHÍCULO');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR VEHÍCULO');
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