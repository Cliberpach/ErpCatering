@extends('layouts.layout')
@section('title-page')
    LISTADO DE PRODUCTOS
@endsection

@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('productos-active', 'active')


@section('section-page')
@include('registros.productos.modals.modal_show')
@include('registros.productos.modals.modal_import_producto')

<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Productos <i class="fa-solid fa-toolbox"></i>
      </h6>
      <button class="btn btn-primary" onclick="goToCrearProducto()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="row mb-3">
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="categoria" style="font-weight: bold;">CATEGORIA</label>
            <select data-placeholder="Seleccionar" name="categoria" id="categoria" class="select2_form" onchange="dtProductos.ajax.reload();">
                @foreach ($categorias as $categoria)
                    <option value="{{$categoria->id}}">{{$categoria->descripcion}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="categoria" style="font-weight: bold;">MARCA</label>
            <select data-placeholder="Seleccionar" name="marca" id="marca" class="select2_form" onchange="dtProductos.ajax.reload();">
                @foreach ($marcas as $marca)
                    <option value="{{$marca->id}}">{{$marca->descripcion}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-warning" onclick="openMdlImportProducto()">
                <i class="fa-solid fa-upload"></i> IMPORTAR
            </button>
            <button class="btn btn-dark" style="margin-left:6px;" onclick="exportarExcelProductos();">
                <i class="fa-solid fa-download"></i> EXPORTAR
            </button>
        </div>
    </div>
    <div class="table-responsive">
        @include('registros.productos.tables.table_list_productos')
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
    let dtProductos    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableProductos();
        iniciarDataTableStocks();
        iniciarSelect2();
        eventsMdlImportarProductos();
    })

    function iniciarDataTableProductos(){
        const urlGetProductos = '{{ route('registros.producto.getProductos') }}';

        dtProductos  =   new DataTable('#table_productos',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetProductos,
                type: 'GET',
                data: function (d) {
                    d.categoria_id  =   $('#categoria').val();
                    d.marca_id      =   $('#marca').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'categoria_nombre', name: 'categoria_nombre' },
                { data: 'marca_nombre', name: 'marca_nombre' },
                { data: 'precio', name: 'precio' },
                { data: 'stock', name: 'stock' },
                { data: 'stock_minimo', name: 'stock_minimo' },
                { data: 'unidad_medida_nombre', name: 'unidad_medida_nombre' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('registros.producto.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                      

                        const urlDelete = `{{ route('registros.colaborador.destroy', ':id') }}`.replace(':id', data.id);

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

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            allowClear: true 
        } );
    }

    function goToCrearProducto(){
        window.location.href = @json(route('registros.producto.create'));
    }


    function eliminarProducto(id){
        toastr.clear();
        let row             =   getRowById(dtProductos,id);
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
                    dtProductos.draw();
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

    function exportarExcelProductos(){
        const categoriaId   = document.getElementById('categoria').value;
        const marcaId       = document.getElementById('marca').value;
        
        const url = '{{ route('registros.producto.excel') }}' + `?categoriaId=${categoriaId}&marcaId=${marcaId}`;
    
        window.location.href = url;    
    }


</script>