
@extends('layouts.layout')
@section('title-page')
    ÓRDENES DE COMPRA - FINANZAS
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
      <h6>Registro de Salidas <i class="fa-solid fa-truck-arrow-right"></i>
      </h6>
        
            <button class="btn btn-primary" onclick="goToRegistrarSalida()">
                <i class="fa-solid fa-plus"></i> NUEVO
            </button>
    </div>
    <div class="table-responsive">
        @include('logistica.registro_salida.tables.table_list_salidas')
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
    let dtSalidas    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        mostrarMsgErrors();
        iniciarDataTableSalidas();
    })

    function mostrarMsgErrors(){
        if("{{ Session::has('registro_salida_error') }}"){
            const msgError  =   "{{ Session::get('registro_salida_error') }}";
            toastr.error(msgError);
        } 
    }

    function iniciarDataTableSalidas(){
        const urlGetSalidas = '{{ route('logistica.registro_salida.getSalidas') }}';

        dtSalidas  =   new DataTable('#table_list_salidas',{
            serverSide: true,
            processing: true,
            responsive:true,
            ajax: {
                url: urlGetSalidas,
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
                {
                    data: 'simbolo_guia_remision',
                    name: 'simbolo_guia_remision',
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).css('font-weight', 'bold');
                    }
                },
                { data: 'colaborador_nombre', name: 'colaborador_nombre' },
                { data: 'almacen_origen_nombre', name: 'almacen_origen_nombre' },
                { data: 'almacen_destino_nombre', name: 'almacen_destino_nombre' },
                { data: 'fecha_registro', name: 'fecha_registro' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        
                        const urlPdf                = `{{ route('logistica.registro_salida.pdf', ':id') }}`.replace(':id', data.id);
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
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlShow(${data.id});">
                                                            <img width="20" height="20" src="{{asset('img/icons/ver/ver1.png')}}" alt="coins"/> Ver
                                                        </a>
                                                    </li>
                                            `;

                            if(!data.guia_remision_id){
                                acciones    +=  `<li>
                                                    <a class="dropdown-item" href="${urlGoToGuiaRemision}" >
                                                        <img width="20" height="20" src="{{asset('img/icons/transporte/transporte1.png')}}" alt="coins"/> Guía Remisión
                                                    </a>
                                                </li>`;
                            }

                            acciones    +=  `</ul></div>`;

                        return  acciones; 
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


    function goToRegistrarSalida(){
        window.location.href = @json(route('logistica.registro_salida.create'));
    }

</script>