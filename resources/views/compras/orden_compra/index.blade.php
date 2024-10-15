
@extends('layouts.layout')
@section('title-page')
    ÓRDENES DE COMPRA
@endsection

@section('compras-collapsed', '')
@section('compras-expanded', 'true')
@section('compras-show', 'show')
@section('orden_compra-active', 'active')

@section('section-page')
@include('compras.orden_compra.modals.modal_orden_compra_show')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Registro de Órdenes Compra <i class="fa-solid fa-cart-shopping"></i>
      </h6>
        
            {{-- <button class="btn btn-primary" onclick="goToRegistrarCotizacionCompra()">
                <i class="fa-solid fa-plus"></i> NUEVO
            </button> --}}
    </div>
    <div class="table-responsive">
        @include('compras.orden_compra.tables.table_list_orden_compra')
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
    let dtOrdenesCompra    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableOrdenCompra();
    })

    function iniciarDataTableOrdenCompra(){
        const urlGetOrdenCompra = '{{ route('compras.orden_compra.getOrdenesCompra') }}';

        dtOrdenesCompra  =   new DataTable('#table_list_orden_compra',{
            serverSide: true,
            processing: true,
            //responsive:true,
            ajax: {
                url: urlGetOrdenCompra,
                type: 'GET',
            },
            order: [[0, 'desc']], 
            columns: [
                { data: 'id', name: 'id', visible: false },
                {
                    data: 'simbolo',
                    name: 'simbolo',
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).css('font-weight', 'bold');
                    }
                },
                {
                    data: 'simbolo_cotizacion_compra',
                    name: 'simbolo_cotizacion_compra',
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).css('font-weight', 'bold');
                    }
                },
                { data: 'colaborador_registrador_nombre', name: 'colaborador_registrador_nombre' },
                { data: 'proveedor_nombre', name: 'proveedor_nombre' },
                { data: 'modalidad_pago', name: 'modalidad_pago' },
                { data: 'proyecto_nombre', name: 'proyecto_nombre' },
                { data: 'orden_compra_documento', name: 'orden_compra_documento' },
                { data: 'orden_compra_direccion_obra', name: 'orden_compra_direccion_obra' },
                { data: 'orden_compra_observacion', name: 'orden_compra_observacion' },
                { data: 'persona_contacto_nombre', name: 'persona_contacto_nombre' },
                { data: 'orden_compra_fecha_entrega', name: 'orden_compra_fecha_entrega' },
                { data: 'orden_compra_terminos_entrega', name: 'orden_compra_terminos_entrega' },
                { data: 'primer_producto_nombre', name: 'primer_producto_nombre' },
                { data: 'orden_compra_estado', name: 'orden_compra_estado' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('compras.orden_compra.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete         = `{{ route('compras.cotizacion_compra.destroy', ':id') }}`.replace(':id', data.id);
                        const urlPdf            = `{{ route('compras.orden_compra.pdf', ':id') }}`.replace(':id', data.id);
                        const urlOrdenCompra    = `{{route('compras.cotizacion_compra.goToOrdenCompra',':id')}}`.replace(':id', data.id);

                        let acciones    =   `
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
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlShowOrdenCompra(${data.id});">
                                                            <i class="fa-solid fa-eye"></i> VER
                                                        </a>
                                                    </li>
                                            `;
                        if(data.orden_compra_estado === 'PENDIENTE'){
                            acciones += `<li>
                                            <a class="dropdown-item" href="${urlEdit}">
                                                <i class="fa-solid fa-pen-to-square"></i> EDITAR
                                            </a>
                                        </li>`;
                        }

                        acciones += `</ul></div>`;

                        return acciones;
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


    function goToRegistrarCotizacionCompra(){
        window.location.href = @json(route('compras.cotizacion_compra.create'));
    }


    function eliminarCotizacionCompra(id){
        toastr.clear();
        let row             =   getRowById(dtOrdenesCompra,id);
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
                    dtOrdenesCompra.draw();
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