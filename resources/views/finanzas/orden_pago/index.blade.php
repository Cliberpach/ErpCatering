
@extends('layouts.layout')
@section('title-page')
    LISTA DE ÓRDENES DE PAGO
@endsection

@section('finanzas-collapsed', '')
@section('finanzas-expanded', 'true')
@section('finanzas-show', 'show')
@section('orden_pago-active', 'active')

@section('section-page')
@include('logistica.registro_salida.modals.modal_show')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Órdenes de Pago <i class="fa-solid fa-cash-register"></i>
      </h6>
        
            {{-- <button class="btn btn-primary" onclick="goToRegistrarSalida()">
                <i class="fa-solid fa-plus"></i> NUEVO
            </button> --}}
    </div>
    <div class="table-responsive">
        @include('finanzas.orden_pago.tables.table_list_orden_pago')
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
    let dtOrdenesPago    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        mostrarMsgErrors();
        iniciarDataTableOrdenesPago();
    })

    function mostrarMsgErrors(){
        if("{{ Session::has('registro_salida_error') }}"){
            const msgError  =   "{{ Session::get('registro_salida_error') }}";
            toastr.error(msgError);
        } 
    }

    function iniciarDataTableOrdenesPago(){
        const urlGetOrdenesPago = '{{ route('finanzas.orden_pago.getOrdenesPago') }}';

        dtOrdenesPago  =   new DataTable('#table_list_orden_pago',{
            serverSide: true,
            processing: true,
            responsive:true,
            ajax: {
                url: urlGetOrdenesPago,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                {
                    data: 'simbolo',
                    name: 'simbolo',
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).css('font-weight', 'bold');
                    }
                },
                {
                    data: 'simbolo_orden_compra',
                    name: 'simbolo_orden_compra',
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).css('font-weight', 'bold');
                    }
                },
                { data: 'colaborador_registrador_nombre', name: 'colaborador_registrador_nombre' },
                { data: 'proveedor_nombre', name: 'proveedor_nombre' },
                { data: 'banco_nombre', name: 'banco_nombre' },
                { data: 'nro_cuenta', name: 'nro_cuenta' },
                { data: 'cci', name: 'cci' },
                { data: 'nro_cuenta_detraccion', name: 'nro_cuenta_detraccion' },
                { data: 'proyecto_nombre', name: 'proyecto_nombre' },
                { data: 'documento', name: 'documento' },
                { data: 'medio_pago', name: 'medio_pago' },
                { data: 'moneda', name: 'moneda' },
                { data: 'subtotal', name: 'subtotal' },
                { data: 'monto_igv', name: 'monto_igv' },
                { data: 'total', name: 'total' },
                { data: 'observacion', name: 'observacion' },
                { data: 'created_at', name: 'created_at' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        
                        const urlPdf                =   `{{ route('finanzas.orden_pago.pdf', ':id') }}`.replace(':id', data.id);
                        const urlGoToGuiaRemision   =   `{{route('logistica.registro_salida.goToGuiaRemision',':id')}}`.replace(':id',data.id);

                        let acciones    =   `
                                                <div class="btn-group dropstart">
                                                <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-grip"></i>
                                                </button>
                                                <ul class="dropdown-menu" style="max-height: 100px; overflow-y: auto;">
                                                    
                                                    <li>
                                                        <a class="dropdown-item" href="${urlPdf}" target="_blank">
                                                            <img width="20" height="20" src="{{asset('img/icons/pdf/pdf2.png')}}" alt="coins"/> PDF
                                                        </a>
                                                    </li>
                                                  
                                            `;

                

                            acciones    +=  `</ul></div>`;

                        return  acciones; 
                    },
                    name: 'actions', 
                    orderable: false, 
                    searchable: false 
                }
            ],
            "columnDefs": [
                {
                    "targets": [0], 
                    "visible": false,
                    "searchable": false 
                }
            ],
            "order": [[0, "desc"]],
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


    function goToRegistrarSalida(){
        window.location.href = @json(route('logistica.registro_salida.create'));
    }

</script>