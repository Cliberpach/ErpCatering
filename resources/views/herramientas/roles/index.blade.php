@extends('layouts.layout')
@section('title-page')
    LISTADO DE ROLES
@endsection

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Roles <i class="fa-solid fa-user"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearRol()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>

    <div class="table-responsive">
        @include('herramientas.roles.tables.table_list_roles')
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
    let dtUsuarios    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableUsuarios();
    })

    function iniciarDataTableUsuarios(){
        const urlGetRoles = '{{ route('herramientas.rol.getRoles') }}';

        dtUsuarios  =   new DataTable('#table_roles',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetRoles,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'fecha_registro', name: 'fecha_registro' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('herramientas.rol.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete = `{{ route('herramientas.usuario.destroy', ':id') }}`.replace(':id', data.id);

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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarUsuario(${data.id})">
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

    function goToCrearRol(){
        window.location.href = @json(route('herramientas.rol.create'));
    }

    function eliminarUsuario(){
        
    }


</script>