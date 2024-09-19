
@extends('layouts.layout')
@section('title-page')
    LISTADO DE SALIDAS
@endsection

@section('logistica-collapsed', '')
@section('logistica-expanded', 'true')
@section('logistica-show', 'show')
@section('registro_salida-active', 'active')

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
        iniciarDataTableSalidas();
    })

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
                { data: 'colaborador_nombre', name: 'colaborador_nombre' },
                { data: 'almacen_origen_nombre', name: 'almacen_origen_nombre' },
                { data: 'almacen_destino_nombre', name: 'almacen_destino_nombre' },
                { data: 'fecha_registro', name: 'fecha_registro' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        // const baseUrlEdit   =   `{{ route('logistica.cotizacion_compra.edit', ['id' => ':id']) }}`;
                        // urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const baseUrlShow   =   `{{ route('logistica.registro_compra.show', ['id' => ':id']) }}`;
                        urlShow             =   baseUrlShow.replace(':id', data.id); 

                        // const urlDelete = `{{ route('logistica.cotizacion_compra.destroy', ':id') }}`.replace(':id', data.id);
                        // const urlPdf    = `{{ route('logistica.cotizacion_compra.pdf', ':id') }}`.replace(':id', data.id);


                        return `
                            <div class="btn-group dropstart">
                            <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 100px; overflow-y: auto;">
                                
                                 <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlShow(${data.id});">
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


    function goToRegistrarSalida(){
        window.location.href = @json(route('logistica.registro_salida.create'));
    }


    function eliminarCotizacionCompra(id){
        toastr.clear();
        let row             =   getRowById(dtSalidas,id);
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
                let urlEliminarCotizacionCompra         =   `{{ route('logistica.cotizacion_compra.destroy', ['id' => ':id']) }}`;
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
                    dtSalidas.draw();
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