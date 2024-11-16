
@extends('layouts.layout')
@section('title-page')
    COTIZACIONES DE COMPRA
@endsection

@section('compras-collapsed', '')
@section('compras-expanded', 'true')
@section('compras-show', 'show')
@section('cotizacion_compra-active', 'active')

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="row">
        <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
            <h6>Registro de Cotizaciones Compra <i class="fa-solid fa-cart-shopping"></i></h6>
        </div>
        <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12" style="display: flex;justify-content:end;">
            <button class="btn btn-primary" onclick="goToRegistrarCotizacionCompra()">
                <i class="fa-solid fa-plus"></i> NUEVO
            </button>
            <button class="btn btn-dark" style="margin-left:6px;" onclick="goToRegistrarCotizacionCompraCompuesta()">
                <i class="fa-solid fa-plus"></i> COMPUESTA
            </button> 
        </div>
    </div>
    <div class="title mb-30 d-flex justify-content-between align-items-center">
        
    </div>
    <div class="table-responsive">
        @include('compras.cotizacion_compra.tables.table_cotizacion_compra')
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
        iniciarDataTableCotizacionCompra();
        mostrarMsgErrors();
    })

    function mostrarMsgErrors(){
        if("{{ Session::has('cotizacion_compra_error') }}"){
            const msgError  =   "{{ Session::get('cotizacion_compra_error') }}";
            toastr.error(msgError);
        } 
    }

    function iniciarDataTableCotizacionCompra(){
        const urlGetCotizacionesCompra = '{{ route('compras.cotizacion_compra.getCotizacionesCompra') }}';

        dtCotizacionesCompra  =   new DataTable('#table_cotizacion_compra',{
            serverSide: true,
            processing: true,
            responsive:true,
            ajax: {
                url: urlGetCotizacionesCompra,
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
                { data: 'simbolo_requerimiento', name: 'simbolo_requerimiento' },
                { data: 'proyecto_nombre', name: 'proyecto_nombre' },
                { data: 'supervisor_nombre', name: 'supervisor_nombre' },
                { data: 'colaborador_nombre', name: 'colaborador_nombre' },
                { data: 'fecha_registro', name: 'fecha_registro' },
                { data: 'estado', name: 'estado' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('compras.cotizacion_compra.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete         = `{{ route('compras.cotizacion_compra.destroy', ':id') }}`.replace(':id', data.id);
                        const urlPdf            = `{{ route('compras.cotizacion_compra.pdf', ':id') }}`.replace(':id', data.id);
                        const urlOrdenCompra    =   `{{route('compras.cotizacion_compra.goToOrdenCompra',':id')}}`.replace(':id', data.id);

                        let acciones    =   `<div class="btn-group dropstart">
                                                <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-grip"></i>
                                                </button>
                                                <ul class="dropdown-menu" style="max-height: 100px; overflow-y: auto;">
                                            `;
                        
                        if(data.estado === "PENDIENTE"){
                            acciones    +=  `<li>
                                                <a class="dropdown-item" href="${urlOrdenCompra}">
                                                    <i class="fa-solid fa-cart-shopping"></i> Orden compra
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>`;
                        }

                        acciones    +=  ` 
                                        <li>
                                            <a class="dropdown-item" href="${urlPdf}" target="_blank">
                                                <i class="fa-solid fa-file-pdf"></i> PDF
                                            </a>
                                        </li>`;

                        if(data.tipo === 'SIMPLE' && data.estado === 'PENDIENTE' ){
                            acciones    +=  `<li>
                                                <a class="dropdown-item" href="${urlEdit}">
                                                    <i class="fa-solid fa-file-pen"></i> Editar
                                                </a>
                                            </li>`;
                        }

                        if(data.estado === 'PENDIENTE' ){
                            
                            acciones    +=  ` <li>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarCotizacionCompra(${data.id})">
                                                <i class="fa-solid fa-trash"></i> Eliminar
                                            </a>
                                        </li>
                                    </ul>
                                    </div>`;

                        }

                       
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

    function goToRegistrarCotizacionCompraCompuesta(){
        window.location.href = @json(route('compras.cotizacion_compra.createCompuesta'));
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