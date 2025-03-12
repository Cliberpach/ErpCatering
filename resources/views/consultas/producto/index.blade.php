@extends('layouts.layout')
@section('title-page')
    CONSULTA DE PRODUCTOS
@endsection

@section('consultas-collapsed', '')
@section('consultas-expanded', 'true')
@section('consultas-show', 'show')
@section('consulta_producto-active', 'active')


@section('section-page')

<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Viajes/Vueltas <i class="fa-solid fa-road"></i>
    </div>
    <div class="row mb-3">
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
            <label for="proyecto" style="font-weight: bold;">ALMACEN</label>
            <select name="almacen" id="almacen" class="select2_form" onchange="dtConsultaProducto.ajax.reload();">
                @foreach ($almacenes as $almacen)
                    <option value="{{$almacen->id}}">{{$almacen->descripcion}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
            <label for="proyecto" style="font-weight: bold;">PROYECTO</label>
            <select data-placeholder="Seleccionar" name="proyecto" id="proyecto" class="select2_form" onchange="dtConsultaProducto.ajax.reload();">
                @foreach ($proyectos as $proyecto)
                    <option value="{{$proyecto->id}}">{{$proyecto->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
            <label for="fecha_inicio" style="font-weight: bold;">FECHA INICIO</label>
            <input value="<?php echo date('Y-m-d'); ?>" type="date" id="fecha_inicio" class="form-control" onchange="cambioFechaInicio();">
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
            <label for="fecha_fin" style="font-weight: bold;">FECHA FIN</label>
            <input value="<?php echo date('Y-m-d'); ?>" type="date" id="fecha_fin" class="form-control" onchange="cambioFechaFin();">
        </div>
    </div>
    <div class="row mb-3 justify-content-end">
        
        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-primary" style="margin-right: 6px;" onclick="exportarPDF();">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </button>
            <button class="btn btn-primary" onclick="exportarExcel();">
                <i class="fa-solid fa-file-excel"></i> Excel
            </button>
        </div>
    </div>
    <div class="table-responsive">
        @include('consultas.producto.tables.table_list')
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
    let dtConsultaProducto    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableConsultaProducto();
        iniciarSelect2();
    })

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            allowClear: true
        } );
    }

    function iniciarDataTableConsultaProducto(){
        const urlGetConsultaProducto = '{{ route('consultas.producto.getConsultaProducto') }}';

        dtConsultaProducto  =   new DataTable('#table_consulta_producto',{
            serverSide: true,
            processing: true,
            pageLength: 50, 

            ajax: {
                url: urlGetConsultaProducto,
                type: 'GET',
                data: function (d) {
                    d.fecha_inicio  =   $('#fecha_inicio').val();
                    d.fecha_fin     =   $('#fecha_fin').val();
                    d.proyecto_id   =   $('#proyecto').val();
                    d.almacen_id    =   $('#almacen').val();
                }
            },
            columns: [
                { data: 'producto_id', name: 'producto_id' },
                { data: 'producto_nombre', name: 'producto_nombre' },
                { data: 'stock_inicial', name: 'stock_inicial' },
                { data: 'ingreso', name: 'ingreso' },
                { data: 'salida', name: 'salida' },
                { data: 'stock_final', name: 'stock_final' }
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

    function goToCrearProducto(){
        window.location.href = @json(route('registros.producto.create'));
    }


    function eliminarProducto(id){
        toastr.clear();
        let row             =   getRowById(dtConsultaProducto,id);
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
                    dtConsultaProducto.draw();
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

    function cambioFechaFin(){
        const fecha_inicio  =   document.querySelector('#fecha_inicio');
        const fecha_fin     =   document.querySelector('#fecha_fin');
        
        if(fecha_fin.value < fecha_inicio.value){
            toastr.error('LA FECHA DE FIN DEBE SER MAYOR A LA FECHA DE INICIO!!');
            fecha_fin.value =   '';
            fecha_fin.focus();
            return;
        }
        dtConsultaProducto.ajax.reload();
    }

    function cambioFechaInicio() {
        const fecha_inicio  =   document.querySelector('#fecha_inicio');
        const fecha_fin     =   document.querySelector('#fecha_fin');
        
        if(fecha_inicio.value > fecha_fin.value){
            toastr.error('LA FECHA DE INICIO DEBE SER MENOR A LA FECHA DE FIN!!');
            fecha_inicio.value =   '';
            fecha_inicio.focus();
            return;
        }
        dtConsultaProducto.ajax.reload();
    }


    function exportarPDF(){
        const fechaInicio   = document.getElementById('fecha_inicio').value;
        const fechaFin      = document.getElementById('fecha_fin').value;
        const proyectoId    = document.getElementById('proyecto').value;
        const almacenId     = document.getElementById('almacen').value;

        const url = '{{ route('consultas.producto.pdf') }}' + `?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&proyecto_id=${proyectoId}&almacen_id=${almacenId}`;
    
        window.open(url, '_blank');
    }


    function exportarExcel(){
        const fechaInicio   = document.getElementById('fecha_inicio').value;
        const fechaFin      = document.getElementById('fecha_fin').value;
        const proyectoId    = document.getElementById('proyecto').value;
        const almacenId     = document.getElementById('almacen').value;
        
        const url = '{{ route('consultas.producto.excel') }}' + `?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&proyecto_id=${proyectoId}&almacen_id=${almacenId}`;
    
        window.location.href = url;    
    }

</script>