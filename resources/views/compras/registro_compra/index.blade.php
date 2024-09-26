
@extends('layouts.layout')
@section('title-page')
    LISTADO DE COMPRAS
@endsection

@section('compras-collapsed', '')
@section('compras-expanded', 'true')
@section('compras-show', 'show')
@section('registro_compra-active', 'active')

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Registro de Compra <i class="fa-solid fa-cart-shopping"></i>
      </h6>
        
            <button class="btn btn-primary" onclick="goToRegistrarCompra()">
                <i class="fa-solid fa-plus"></i> NUEVO
            </button>
    </div>
    <div class="table-responsive">
        @include('compras.registro_compra.tables.table_list_compras')
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
    let dtCotizacionesCompra    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableCompra();
    })

    function iniciarDataTableCompra(){
        const urlGetCompras = '{{ route('compras.registro_compra.getCompras') }}';

        dtCotizacionesCompra  =   new DataTable('#table_list_compras',{
            serverSide: true,
            processing: true,
            responsive:true,
            ajax: {
                url: urlGetCompras,
                type: 'GET',
            },
            columns: [
                {
                    data: 'simbolo',
                    name: 'simbolo',
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).css('font-weight', 'bold');
                    }
                },
                { data: 'colaborador_nombre', name: 'colaborador_nombre' },
                { data: 'documento', name: 'documento' },
                { 
                    data: 'precios_igv', 
                    name: 'precios_igv',
                    render: function (data, type, row) {
                        if(data == 1){
                            return "CON IGV";
                        }
                        if (data == 0) {
                            return "SIN IGV";
                        }
                    }
                },
                { 
                    data: 'subtotal_soles', 
                    name: 'subtotal_soles',
                    render: function (data, type, row) {
                        return type === 'display' ? (Number(data) || 0).toFixed(2) : data;
                    }
                },
                { 
                    data: 'monto_igv_soles', 
                    name: 'monto_igv_soles',
                    render: function (data, type, row) {
                        return type === 'display' ? (Number(data) || 0).toFixed(2) : data;
                    }
                },
                { 
                    data: 'total_soles', 
                    name: 'total_soles',
                    render: function (data, type, row) {
                        return type === 'display' ? (Number(data) || 0).toFixed(2) : data;
                    }
                },
                { data: 'fecha_registro', name: 'fecha_registro' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('compras.cotizacion_compra.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const baseUrlShow   =   `{{ route('compras.registro_compra.show', ['id' => ':id']) }}`;
                        urlShow             =   baseUrlShow.replace(':id', data.id); 

                        const urlDelete = `{{ route('compras.cotizacion_compra.destroy', ':id') }}`.replace(':id', data.id);
                        const urlPdf    = `{{ route('compras.registro_compra.pdf', ':id') }}`.replace(':id', data.id);


                        return `
                            <div class="btn-group dropstart">
                            <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 100px; overflow-y: auto;">
                                 <li>
                                    <a class="dropdown-item" href="${urlPdf}" target="_blank">
                                        <i class="fa-solid fa-file-pdf"></i> PDF
                                    </a>
                                </li>
                                 <li>
                                    <a class="dropdown-item" href="${urlShow}">
                                        <i class="fa-solid fa-eye"></i> Ver
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


    function goToRegistrarCompra(){
        window.location.href = @json(route('compras.registro_compra.create'));
    }


    function eliminarCotizacionCompra(id){
        toastr.clear();
        let row             =   getRowById(dtCotizacionesCompra,id);
        let message         =   '';

        message =   `Desea eliminar la cotización de compra N°${id}`;

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
                html: 'Eliminando cotización de compra...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlEliminarCotizacionCompra         =   `{{ route('compras.cotizacion_compra.destroy', ['id' => ':id']) }}`;
                urlEliminarCotizacionCompra             =   urlEliminarCotizacionCompra.replace(':id', id);
                const token                             =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlEliminarCotizacionCompra, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtCotizacionesCompra.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR COTIZACIÓN DE COMPRA');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR COTIZACIÓN DE COMPRA');
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