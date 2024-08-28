@extends('layouts.layout')
@section('title-page')
    LISTADO DE COLABORADORES
@endsection

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Colaboradores <i class="fa-solid fa-user"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearColaborador()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="table-responsive">
        @include('herramientas.colaboradores.tables.table_list_colaboradores')
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
    let dtColaboradores    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableUsuarios();
    })

    function iniciarDataTableUsuarios(){
        const urlGetColaboradores = '{{ route('herramientas.colaborador.getColaboradores') }}';

        dtColaboradores  =   new DataTable('#table_colaboradores',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetColaboradores,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'direccion', name: 'direccion' },
                { data: 'telefono', name: 'telefono' },
                { data: 'nro_documento', name: 'nro_documento' },
                { data: 'horas_semana', name: 'horas_semana' },
                { data: 'pago_semana', name: 'pago_semana' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('herramientas.colaborador.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete = `{{ route('herramientas.colaborador.destroy', ':id') }}`.replace(':id', data.id);

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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarColaborador(${data.id})">
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

    function goToCrearColaborador(){
        window.location.href = @json(route('herramientas.colaborador.create'));
    }

    function getRowById(id) {
        let data = dtColaboradores.rows().data();
        let rowData = null;

        for (let i = 0; i < data.length; i++) {
            if (data[i].id === id) {
                rowData = data[i];
                break;
            }
        }

        return rowData;
    }


    function eliminarColaborador(id){
        toastr.clear();
        let row             =   getRowById(id);
        let message         =   '';
        let tipo_documento  =   '';

        if(row.tipo_documento_id == 1){
            tipo_documento  =   'DNI';
        }

        if(row.tipo_documento_id == 2){
            tipo_documento  =   'CARNET EXTRANJERÍA';
        }


        message =   `Desea eliminar el colaborador: ${row.nombre}, ${tipo_documento}:${row.nro_documento}`;

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
                html: 'Eliminando colaborador...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteColaborador    =   `{{ route('herramientas.colaborador.destroy', ['id' => ':id']) }}`;
                urlDeleteColaborador        =   urlDeleteColaborador.replace(':id', id);
                const token                     =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteColaborador, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtColaboradores.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR COLABORADOR');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR COLABORADOR');
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