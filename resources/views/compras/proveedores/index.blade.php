@extends('layouts.layout')
@section('title-page')
    LISTADO DE PROVEEDORES
@endsection

@section('compras-collapsed', '')
@section('compras-expanded', 'true')
@section('compras-show', 'show')
@section('proveedores-active', 'active')


@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Proveedores <i class="fa-solid fa-truck-field"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearProveedor()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="table-responsive">
        @include('compras.proveedores.tables.table_list_proveedores')
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
    let dtProveedores    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableColaboradores();
    })

    function iniciarDataTableColaboradores(){
        const urlGetProveedores = '{{ route('compras.proveedor.getProveedores') }}';

        dtProveedores  =   new DataTable('#table_proveedores',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetProveedores,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'tipo_documento_descripcion', name: 'tipo_documento_descripcion' },
                { data: 'nro_documento', name: 'nro_documento' },
                { data: 'nombre', name: 'nombre' },
                { data: 'direccion', name: 'direccion' },
                { data: 'telefono', name: 'telefono' },
                { data: 'correo', name: 'correo' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('compras.proveedor.edit', ['id' => ':id']) }}`;
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarProveedor(${data.id})">
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

    function goToCrearProveedor(){
        window.location.href = @json(route('compras.proveedor.create'));
    }


    function eliminarProveedor(id){
        toastr.clear();
        let row             =   getRowById(dtProveedores,id);
        let message         =   '';

        if(!row){
            toastr.error('NO SE ENCUENTRA EL PROVEEDOR EN EL DATATABLE');
            return;
        }
      
        message =   `Desea eliminar el proveedor: ${row.nombre}, ${row.tipo_documento_descripcion}:${row.nro_documento}`;

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
                html: 'Eliminando proveedor...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteProveedor    =   `{{ route('compras.proveedor.destroy', ['id' => ':id']) }}`;
                urlDeleteProveedor        =   urlDeleteProveedor.replace(':id', id);
                const token               =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteProveedor, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtProveedores.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR PROVEEDOR');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR PROVEEDOR');
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