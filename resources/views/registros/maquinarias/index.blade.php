@extends('layouts.layout')
@section('title-page')
    LISTADO DE MAQUINARIAS
@endsection

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Maquinarias <i class="fa-solid fa-toolbox"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearMaquinaria()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="table-responsive">
        @include('registros.maquinarias.tables.table_list_maquinarias')
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
    let dtMaquinarias    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableProductos();
    })

    function iniciarDataTableProductos(){
        const urlGetMaquinarias = '{{ route('registros.maquinaria.getMaquinarias') }}';

        dtMaquinarias  =   new DataTable('#table_maquinarias',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetMaquinarias,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'tipo_gasto_nombre', name: 'tipo_gasto_nombre' },
                { data: 'costo_gasto', name: 'costo_gasto' },
                { data: 'observacion', name: 'observacion' },
                { data: 'fecha_registro', name: 'fecha_registro' },
                { data: 'fecha_modificacion', name: 'fecha_modificacion' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('registros.maquinaria.edit', ['id' => ':id']) }}`;
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarMaquinaria(${data.id})">
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

    function goToCrearMaquinaria(){
        window.location.href = @json(route('registros.maquinaria.create'));
    }


    function eliminarMaquinaria(id){
        toastr.clear();
        let row             =   getRowById(dtMaquinarias,id);
        let message         =   '';

        message =   `Desea eliminar la maquinaria: ${row.nombre}`;

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
                html: 'Eliminando maquinaria...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteMaquinaria     =   `{{ route('registros.maquinaria.destroy', ['id' => ':id']) }}`;
                urlDeleteMaquinaria         =   urlDeleteMaquinaria.replace(':id', id);
                const token                 =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteMaquinaria, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtMaquinarias.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR MAQUINARIA');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR MAQUINARIA');
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