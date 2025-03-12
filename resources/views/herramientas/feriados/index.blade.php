@extends('layouts.layout')
@section('title-page')
    LISTADO DE FERIADOS
@endsection

@section('herramientas-collapsed', '')
@section('herramientas-expanded', 'true')
@section('herramientas-show', 'show')
@section('feriados-active', 'active')


@section('section-page')

<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Feriados <i class="fa-solid fa-toolbox"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearFeriado()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
  
    <div class="table-responsive">
        @include('herramientas.feriados.tables.tbl_list_feriados')
    </div>
</div>
@endsection

@if(Session::has('message_success'))
<script>
    var message = "{{ Session::get('message_success') }}";
    toastr.success(message, 'OPERACIÓN COMPLETADA');
</script>
@endif

<script>
    let dtFeriados    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableFeriados();
        iniciarDataTableStocks();
        iniciarSelect2();
        eventsMdlImportarProductos();
    })

    function iniciarDataTableFeriados(){
        const urlGetFeriados = '{{ route('herramientas.feriados.getFeriados') }}';

        dtFeriados  =   new DataTable('#tbl_list_feriados',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetFeriados,
                type: 'GET',
                data: function (d) {
                    d.categoria_id  =   $('#categoria').val();
                    d.marca_id      =   $('#marca').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'fecha', name: 'fecha' },
                { data: 'anio', name: 'anio' },
                { data: 'descripcion', name: 'descripcion' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('herramientas.feriados.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete = `{{ route('herramientas.feriados.destroy', ':id') }}`.replace(':id', data.id);

                        return `
                            <div class="btn-group dropstart">
                            <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 150px; overflow-y: auto;">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlShowProducto(${data.id})">
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarFeriado(${data.id})">
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

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            allowClear: true 
        } );
    }

    function goToCrearFeriado(){
        window.location.href = @json(route('herramientas.feriados.create'));
    }


    function eliminarFeriado(id){
        toastr.clear();
        let row             =   getRowById(dtFeriados,id);
        let message         =   '';
        let tipo_documento  =   '';

        message =   `Desea eliminar el feriado: ${row.descripcion}`;

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
                html: 'Eliminando feriado...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteFeriado    =   `{{ route('herramientas.feriados.destroy', ['id' => ':id']) }}`;
                urlDeleteFeriado        =   urlDeleteFeriado.replace(':id', id);
                const token             =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteFeriado, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtFeriados.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR FERIADO');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR FERIADO');
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

    function exportarExcelProductos(){
        const categoriaId   = document.getElementById('categoria').value;
        const marcaId       = document.getElementById('marca').value;
        
        const url = '{{ route('registros.producto.excel') }}' + `?categoriaId=${categoriaId}&marcaId=${marcaId}`;
    
        window.location.href = url;    
    }


</script>