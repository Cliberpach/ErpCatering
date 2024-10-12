@extends('layouts.layout')
@section('title-page')
    CONVERTIR REQUERIMIENTO A COTIZACIÓN COMPRA
@endsection

@section('compras-collapsed', '')
@section('compras-expanded', 'true')
@section('compras-show', 'show')
@section('cotizacion_compra-active', 'active')

@section('section-page')

@include('logistica.lista_requerimientos.modals.modal_requerimiento_to_cotizacion_add_producto')
@include('logistica.lista_requerimientos.modals.modal_requerimiento_to_cotizacion_edit_producto')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos de la Cotización de Compra <i class="fa-solid fa-toolbox"></i></h6>
    </div>
    <div class="card-body">
        @include('logistica.lista_requerimientos.forms.form_requerimiento_to_cotizacion')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formRequerimientoToCotizacionCompra">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>

    let dtProductos         =   null;
    let dtRequerimientoToCotizacionDetalle     =   null;
    const lstCotizacionCompra  =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        iniciarDataTableProductos();
        iniciarDataTableRequerimientoToCotizacionDetalle();
        cargarDetallePrevio();
        mostrarMsgErrors();
        events();
    })

    function events(){
        eventsMdlReqToCotEditProducto();

        document.querySelector('#formRequerimientoToCotizacionCompra').addEventListener('submit',(e)=>{
            e.preventDefault();
            const validacion    =   validacionRegistrarRequerimientoToCotizacion();
            if(validacion){
                registrarRequerimientoToCotizacion();
            }
        })

      

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('logistica.lista_requerimientos.index')}}';
                window.location.href    =   rutaIndex;
            }

            if (e.target.closest('.btnAgregarProducto')) {
                toastr.clear();
                const inputCantidad =   document.querySelector('#cantidad'); 
                const validacion    =   validacionAgregarProducto();

                if(validacion){
                    mostrarAnimacion1();
                    agregarProducto({...producto_elegido},inputCantidad.value);
                    limpiarFormSelectProducto();
                    ocultarAnimacion1();
                }
              
            }
        })

    }

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            allowClear: true  
        } );
    }

    function mostrarMsgErrors(){
        if("{{ Session::has('cotizacion_compra_error') }}"){
            const msgError  =   "{{ Session::get('cotizacion_compra_error') }}";
            toastr.error(msgError);
        } 
    }

    function iniciarDataTableProductos(){
        const urlGetProductos   =   @json(route('registros.producto.getProductos'));
        
        dtProductos  =   new DataTable('#table_productos',{
            serverSide: true,  
            processing: true,  
            ajax: {
                url: urlGetProductos, 
                type: 'GET',  
                data: function(d) {
                    d.categoria_id  =   $('#categoria').val();  
                    d.marca_id      =   $('#marca').val();  
                },
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'marca_nombre', name: 'marca_nombre' },
                { data: 'categoria_nombre', name: 'categoria_nombre' },
                { data: 'stock', name: 'Stock' }
            ],
            createdRow: function(row, data, dataIndex) {
                $(row).css('cursor', 'pointer');
                
                $(row).attr('onclick', 'seleccionarProducto(' + data.id + ')');
            },
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

    function iniciarDataTableRequerimientoToCotizacionDetalle(){
        dtRequerimientoToCotizacionDetalle  =   new DataTable('#table_requerimiento_to_cotizacion_detalle',{
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

    function validacionAgregarProducto(){
        
        if(!producto_elegido.producto_id){
            toastr.error('DEBE SELECCIONAR UN PRODUCTO!!');
            return false;
        }

        const inputCantidad =   document.querySelector('#cantidad'); 
        if(!inputCantidad.value){
            toastr.error('DEBE INGRESAR UNA CANTIDAD!!');
            return false;
        }
        if(inputCantidad.value == 0){
            toastr.error('LA CANTIDAD DEBE SER MAYOR A 0!!');
            return false;
        }

        return true;
    }

    function validacionRegistrarRequerimientoToCotizacion(){
        if(lstCotizacionCompra.length === 0){
            toastr.error('EL DETALLE DE LA COTIZACIÓN ESTÁ VACÍO!!!');
            return false;
        }
        return true;
    }

    function agregarProducto(producto,cantidad){
        producto.cantidad   =   cantidad;

        const indiceProducto    =   lstCotizacionCompra.findIndex((p)=>{
            return p.producto_id == producto.producto_id;
        })

        if(indiceProducto !== -1){
            toastr.error('EL PRODUCTO YA EXISTE EN EL DETALLE');
            return;
        }

        lstCotizacionCompra.push(producto);
        limpiarTabla('table_requerimiento_to_cotizacion_detalle');
        destruirDataTableRequerimientoToCotizacionDetalle();
        pintarTableRequerimientoToCotizacionDetalle(lstCotizacionCompra);
        iniciarDataTableRequerimientoToCotizacionDetalle();
        toastr.info('PRODUCTO AGREGADO AL DETALLE');
    }

    function pintarTableRequerimientoToCotizacionDetalle(lstItems){
        let filas   =   ``;
        lstItems.forEach((producto)=>{
            filas   +=  `<tr>
                            <th>
                                <div style="display:flex;justify-content:center;gap:5px;">
                                    <i class="fas fa-edit btn btn-warning btnEditItem" data-producto-id="${producto.producto_id}"></i>
                                    <i class="fas fa-trash-alt btn btn-danger btnDeleteItem" data-producto-id="${producto.producto_id}"></i>
                                </div>
                            </th>
                            <td>${producto.producto_nombre}</td>
                            <td>${producto.categoria_nombre}</td>
                            <td>${producto.marca_nombre}</td>
                            <td>${producto.producto_unidad_medida}</td>
                            <td>${producto.cantidad}</td>
                        </tr>`;
        })

        const tbody =   document.querySelector('#table_requerimiento_to_cotizacion_detalle tbody');
        tbody.innerHTML =   filas;
    }

    
    function destruirDataTableProductos(){
        if(dtProductos){
            dtProductos.destroy();
            dtProductos =   null;
        }
    }

    function destruirDataTableRequerimientoToCotizacionDetalle(){
        if(dtRequerimientoToCotizacionDetalle){
            dtRequerimientoToCotizacionDetalle.destroy();
            dtRequerimientoToCotizacionDetalle =   null;
        }
    }

    function pintarProductos(lstProductos){
        let filas   =   ``;
        lstProductos.forEach((producto)=>{
            filas   +=  `<tr style="cursor:pointer;" onclick="seleccionarProducto(${producto.producto_id});">
                            <th>${producto.producto_id}</th>
                            <td>${producto.producto_nombre}</td>
                            <td>${producto.categoria_nombre}</td>
                            <td>${producto.marca_nombre}</td>
                            <td>${producto.producto_stock}</td>
                        </tr>`;
        })

        const tbody =   document.querySelector('#table_productos tbody');
        tbody.innerHTML =   filas;
    }


    function registrarRequerimientoToCotizacion(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA GENERAR LA COTIZACIÓN DE COMPRA?",
        text: "Se convertirá el requerimiento a cotización de compra!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, GENERAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            const token                                 =   document.querySelector('input[name="_token"]').value;
            const formRequerimientoToCotizacionCompra   =   document.querySelector('#formRequerimientoToCotizacionCompra');
            const formData                              =   new FormData(formRequerimientoToCotizacionCompra);
            const urlRequerimientoToCotizacion          =   @json(route('logistica.lista_requerimientos.requerimientoToCotizacion'));

            formData.append('lstCotizacionCompra',JSON.stringify(lstCotizacionCompra))
            formData.append('requerimiento_id',@json($requerimiento->id))

            Swal.fire({
                title: 'Cargando...',
                html: 'Convirtiendo requerimiento a cotización de compra...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRequerimientoToCotizacion, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                        body: formData
                                    });

                const   res =   await response.json();
                
                console.log(res);
                
                if(response.status === 422){
                    if('errors' in res){
                        pintarErroresValidacion(res.errors);
                    }
                    Swal.close();
                    return;
                }
                
                if(res.success){
                    const cotizacion_compra_index       =   @json(route('compras.cotizacion_compra.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href                =   cotizacion_compra_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN GENERAR COTIZACIÓN DE COMPRA');
                Swal.close();
            }
          

        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire({
            title: "OPERACIÓN CANCELADA",
            text: "NO SE REALIZARON ACCIONES",
            icon: "error"
            });
        }
        });
    }

    function pintarErroresValidacion(objErroresValidacion){
        for (let clave in objErroresValidacion) {
            const pError        =   document.querySelector(`.${clave}_error`);
            pError.textContent  =   objErroresValidacion[clave][0];
        }
    }

    function cargarDetallePrevio(){
        const requerimiento_detalle    =   @json($requerimiento_detalle);
        
        requerimiento_detalle.forEach((rd)=>{
            const producto  =   {
                                    cantidad:rd.cantidad,
                                    categoria_nombre:rd.categoria_nombre,
                                    marca_nombre:rd.marca_nombre,
                                    producto_id:rd.producto_id,
                                    producto_nombre:rd.producto_nombre,
                                    producto_unidad_medida:rd.producto_unidad_medida,
                                }
            lstCotizacionCompra.push(producto);
        })

        pintarTableRequerimientoToCotizacionDetalle(lstCotizacionCompra);
    }

   
  
    
   

</script>


