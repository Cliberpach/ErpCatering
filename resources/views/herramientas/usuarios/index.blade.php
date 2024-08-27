@extends('layouts.layout')
@section('title-page')
    LISTADO DE USUARIOS
@endsection

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Usuarios <i class="fa-solid fa-user"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearUsuario()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    @include('herramientas.usuarios.tables.table_list_usuarios')
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
        dtUsuarios  =   new DataTable('#table_usuarios',{
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

    function goToCrearUsuario(){
        window.location.href = @json(route('herramientas.usuario.create'));
    }


</script>