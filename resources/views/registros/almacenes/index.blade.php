@extends('layouts.layout')
@section('title-page')
    LISTADO DE ALMACÉNES
@endsection

@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('almacenes-active', 'active')

@section('section-page')

@include('registros.almacenes.modals.modal_create_almacen')
@include('registros.almacenes.modals.modal_edit_almacen')
@include('registros.almacenes.modals.modal_asignar_proyecto')


<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Almacénes <i class="fa-solid fa-warehouse" style="color: rgb(7, 45, 168);"></i>
      </h6>
      <button class="btn btn-primary" onclick="openMdlNuevoAlmacen()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="table-responsive">
        @include('registros.almacenes.tables.table_list_almacenes')
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
    let dtAlmacenes    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableAlmacenes();
        iniciarSelect2();
        events();
    })

    function events(){
        eventsMdlCreateAlmacen();
        eventsMdlEditAlmacen();
        eventsMdlAsignarProyecto();
    }

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            allowClear: true,
        } );
    }

    function iniciarDataTableAlmacenes(){
        const urlGetAlmacenes = '{{ route('registros.almacen.getAlmacenes') }}';

        dtAlmacenes  =   new DataTable('#table_almacenes',{
            serverSide: true,
            processing: true,
            pageLength: 50, 

            ajax: {
                url: urlGetAlmacenes,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'proyecto_nombre',name:'proyecto_nombre'},
                { data: 'fecha_registro', name: 'fecha_registro' },
                { data: 'fecha_modificacion', name: 'fecha_modificacion' },
                {
                    data: null, 
                    render: function(data, type, row) {
                      
                        return `
                            <div class="btn-group dropstart">
                            <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 150px; overflow-y: auto;">
                                 <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlAsignarProyecto(${data.id})">
                                        <i class="fa-solid fa-diagram-project"></i> Asignar Proyecto
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlEditAlmacen(${data.id})">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarAlmacen(${data.id})">
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


    function eliminarAlmacen(id){
        toastr.clear();
        let row             =   getRowById(dtAlmacenes,id);
        let message         =   '';
        let tipo_documento  =   '';

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: `DESEA ELIMINAR EL ALMACÉN?`,
        text: `Almacén: ${row.nombre}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            
            Swal.fire({
                title: 'Cargando...',
                html: 'Eliminando almacén...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteAlmacen    =   `{{ route('registros.almacen.destroy', ['id' => ':id']) }}`;
                urlDeleteAlmacen        =   urlDeleteAlmacen.replace(':id', id);
                const token             =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteAlmacen, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtAlmacenes.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR ALMACÉN');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR ALMACÉN');
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