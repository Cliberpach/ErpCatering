
@extends('layouts.layout')
@section('title-page')
    LISTA DE REQUERIMIENTOS
@endsection

@section('logistica-collapsed', '')
@section('logistica-expanded', 'true')
@section('logistica-show', 'show')
@section('lista_requerimientos-active', 'active')

@section('section-page')
@include('logistica.lista_requerimientos.modals.modal_show')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
        <h6>Lista de Requerimientos <i class="fa-solid fa-bell-concierge"></i></h6>
        
        @role('SUPERVISOR')
            <button class="btn btn-primary" onclick="goToRegistrarRequerimiento()">
                <i class="fa-solid fa-plus"></i> NUEVO
            </button>
        @endrole
    </div>
    <div class="table-responsive">
        @include('requerimientos.requerimientos.tables.table_list_requerimientos')
    </div>
</div>
<!-- end card -->
@endsection

<script>
    let dtRequerimientos    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableRequerimientos();
        mostrarMsgErrors();
    })

    function mostrarMsgErrors(){
        if("{{ Session::has('requerimiento_error') }}"){
            const msgError  =   "{{ Session::get('requerimiento_error') }}";
            toastr.error(msgError);
        } 
    }

    function iniciarDataTableRequerimientos(){
        const urlGetRequerimientos = '{{ route('logistica.lista_requerimientos.getRequerimientos') }}';

        dtRequerimientos  =   new DataTable('#table_list_requerimientos',{
            serverSide: true,
            processing: true,
            responsive:true,
            ajax: {
                url: urlGetRequerimientos,
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
                { data: 'proyecto_nombre', name: 'proyecto_nombre' },
                { data: 'supervisor_nombre', name: 'supervisor_nombre' },
                { data: 'proveedor_nombre', name: 'proveedor_nombre' },
                { data: 'fecha_registro', name: 'fecha_registro' },
                { data: 'fecha_atencion', name: 'fecha_atencion' },
                { data: 'primer_producto_nombre', name: 'primer_producto_nombre' },
                { data: 'estado', name: 'estado' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('requerimientos.requerimientos.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete = `{{ route('requerimientos.requerimientos.destroy', ':id') }}`.replace(':id', data.id);
                        
                        let acciones    =   `
                                            <div class="btn-group dropstart">
                                            <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa-solid fa-grip"></i>
                                            </button>
                                            <ul class="dropdown-menu" style="max-height: 100px; overflow-y: auto;">
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlShowRequerimiento(${data.id})">
                                                        <i class="fa-solid fa-eye"></i> Ver
                                                    </a>
                                                </li>
                                        `;

                        if(data.estado === 'PENDIENTE'){
                             acciones    +=  ` <li>
                                                    <a class="dropdown-item" href="javascript:void(0);" onclick="generarCotizacion(${data.id})">
                                                        <i class="fa-solid fa-clipboard-list"></i> Cotizar
                                                    </a>
                                                </li>`;
                        }

                        acciones    +=  `</ul></div>`;

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


    function goToRegistrarRequerimiento(){
        window.location.href = @json(route('requerimientos.requerimientos.create'));
    }


    function eliminarRequerimiento(id){
        toastr.clear();
        let row             =   getRowById(dtRequerimientos,id);
        let message         =   '';
        let text            =   `SUPERVISOR: ${row.supervisor_nombre} - PROYECTO: ${row.proyecto_nombre}`;

        message =   `Desea eliminar el requerimiento N°${id}`;

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: message,
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            
            Swal.fire({
                title: 'Cargando...',
                html: 'Eliminando requerimiento...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlEliminarRequerimiento         =   `{{ route('requerimientos.requerimientos.destroy', ['id' => ':id']) }}`;
                urlEliminarRequerimiento             =   urlEliminarRequerimiento.replace(':id', id);
                const token                             =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlEliminarRequerimiento, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtRequerimientos.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR REQUERIMIENTO');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR REQUERIMIENTO');
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

    function generarCotizacion(requerimiento_id){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "Desea generar una nueva cotización de compra?",
        text: "Se le redireccionará al listado de cotizaciones de compra!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, generar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {

        if (result.isConfirmed) {
           
            Swal.fire({
                title: 'Cargando...',
                html: 'Generando cotización de compra...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlGenerarCotizacion     =   `{{ route('logistica.lista_requerimientos.generarCotizacion') }}`;
                const token                  =   document.querySelector('input[name="_token"]').value;
                const formData               =   new FormData();

                formData.append('requerimiento_id',requerimiento_id);

                const response  =   await fetch(urlGenerarCotizacion, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                        body:formData
                                    });

                const   res =   await response.json();

                if(res.success){
                    const cotizacion_compra_index = '{{ route('compras.cotizacion_compra.index') }}';
                    window.location.href = cotizacion_compra_index; 
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL GENERAR COTIZACIÓN DE COMPRA');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN GENERAR COTIZACIÓN DE COMPRA');
            }finally{
                Swal.close();
            }



        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire({
            title: "Cancelled",
            text: "Your imaginary file is safe :)",
            icon: "error"
            });
        }
        }); 
    }

</script>

