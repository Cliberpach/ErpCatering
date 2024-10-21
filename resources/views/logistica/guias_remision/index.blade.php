
@extends('layouts.layout')
@section('title-page')
    LISTADO DE GUÍAS DE REMISIÓN
@endsection

@section('logistica-collapsed', '')
@section('logistica-expanded', 'true')
@section('logistica-show', 'show')
@section('guias_remision-active', 'active')

@section('section-page')
@include('logistica.registro_salida.modals.modal_show')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Guías de Remisión <i class="fa-solid fa-truck-arrow-right"></i>
      </h6>
        
            {{-- <button class="btn btn-primary" onclick="goToRegistrarGuiaRemision()">
                <i class="fa-solid fa-plus"></i> NUEVO
            </button> --}}
    </div>
    <div class="table-responsive">
        @include('logistica.guias_remision.tables.table_list_guias_remision')
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
    let dtGuiasRemision    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        mostrarMsgErrors();
        iniciarDataTableGuiasRemision();
    })

    function mostrarMsgErrors(){
        if("{{ Session::has('registro_salida_error') }}"){
            const msgError  =   "{{ Session::get('registro_salida_error') }}";
            toastr.error(msgError);
        } 
    }

    function iniciarDataTableGuiasRemision(){
        const urlGetGuiasRemision = '{{ route('logistica.guias_remision.getGuiasRemision') }}';

        dtGuiasRemision  =   new DataTable('#table_list_guias_remision',{
            serverSide: true,
            processing: true,
            responsive:true,
            ajax: {
                url: urlGetGuiasRemision,
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
                    data: 'simbolo_salida',
                    name: 'simbolo_salida',
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).css('font-weight', 'bold');
                    }
                },
                { data: 'fecha_registro', name: 'fecha_registro' },
                { data: 'fecha_traslado', name: 'fecha_traslado' },
                { data: 'peso_total', name: 'peso_total' },
                { data: 'nro_bultos', name: 'nro_bultos' },
                { data: 'serie', name: 'serie' },
                { data: 'ticket', name: 'ticket' },
                { data: 'estado', name: 'estado' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        
                        const urlPdf                = `{{ route('logistica.guias_remision.pdf', ':id') }}`.replace(':id', data.id);

                        return `
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="sendSunat(${data.id});">
                                        <img width="20" height="20" src="{{asset('img/icons/enviar/enviar1.png')}}" alt="coins"/> Sunat
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

    function sendSunat(guia_remision_id){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "Desea envíar la guía de remisión a Sunat?",
        text: "Operación no reversible!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, enviar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            
        
        try {

            toastr.clear();
            const token                 =   document.querySelector('input[name="_token"]').value;
            const urlSendSunat          =   `{{ route('logistica.guias_remision.send_sunat') }}`;

            const formData              =   new FormData();
            formData.append('guia_remision_id',guia_remision_id);

            mostrarAnimacion1();

            const response = await fetch(urlSendSunat, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token // Token CSRF para proteger la solicitud
                                },
                                body: formData
                            });

            const   res =   await response.json();
                                
            if(res.success){
                dtGuiasRemision.draw();
                toastr.success(res.message,'OPERACIÓN COMPLETADA');
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR');
            }

              
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VER SALIDA');
        }finally{
            ocultarAnimacion1();
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


    function goToRegistrarGuiaRemision(){
        window.location.href = @json(route('logistica.registro_salida.create'));
    }

</script>