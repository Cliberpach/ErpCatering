@extends('layouts.layout')
@section('title-page')
<i class="fa-solid fa-network-wired" style="color: rgb(25, 62, 207);"></i> LISTADO DE TAREAS
@endsection

@section('plan_proyecto-collapsed', '')
@section('plan_proyecto-expanded', 'true')
@section('plan_proyecto-show', 'show')
@section('tareas-active', 'active')


@section('section-page')
@include('plan_proyecto.tareas.modals.modal_show_tarea')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Tareas <i class="fa-solid fa-list-check"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearTarea()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <label for="proyecto" style="font-weight: bold;">PROYECTO</label>
            <select name="proyecto" id="proyecto" class="select2_form" onchange="dtTareas.ajax.reload();">
                @foreach ($proyectos as $proyecto)
                    <option value="{{$proyecto->id}}">{{$proyecto->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="table-responsive">
        @include('plan_proyecto.tareas.tables.table_list_tareas')
    </div>
</div>
@endsection

<script>
    let dtTareas    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableTareas();
        iniciarSelect2();
    })

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        } );
    }

    function iniciarDataTableTareas(){
        const urlGetTareas = '{{ route('plan_proyecto.tarea.getTareas') }}';

        dtTareas  =   new DataTable('#table_tareas',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetTareas,
                type: 'GET',
                data: function (d) {
                    d.proyecto_id   =   $('#proyecto').val();
                }
            },
            columns: [
                { data: 'id', name: 'id', visible: false },  
                { data: 'nombre', name: 'nombre' },
                { data: 'fecha_inicio', name: 'fecha_inicio' },
                { data: 'fecha_fin', name: 'fecha_fin' },
                { data: 'avance', name: 'avance' },
                { data: 'dias_faltantes', name: 'dias_faltantes' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('plan_proyecto.tarea.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                      

                        const urlDelete = `{{ route('registros.colaborador.destroy', ':id') }}`.replace(':id', data.id);

                        return `
                            <div class="btn-group dropstart">
                            <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 150px; overflow-y: auto;">
                                 <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlShowTarea(${data.id})">
                                        <i class="fa-solid fa-eye"></i> Ver
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="${urlEdit}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarProducto(${data.id})">
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

    function goToCrearTarea(){
        const proyecto_id   =   $('#proyecto').val();
        if(!proyecto_id){
            toastr.error('DEBE SELECCIONAR UN PROYECTO PARA PODER CREAR UNA TAREA');
            return;
        }
        window.location.href = @json(route('plan_proyecto.tarea.create', ':id')).replace(':id', proyecto_id);
    }


    function eliminarProducto(id){
        toastr.clear();
        let row             =   getRowById(dtTareas,id);
        let message         =   '';
        let tipo_documento  =   '';

        message =   `Desea eliminar el producto: ${row.nombre}`;

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
                html: 'Eliminando producto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteProducto    =   `{{ route('registros.producto.destroy', ['id' => ':id']) }}`;
                urlDeleteProducto        =   urlDeleteProducto.replace(':id', id);
                const token              =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteProducto, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtTareas.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR PRODUCTO');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR PRODUCTO');
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