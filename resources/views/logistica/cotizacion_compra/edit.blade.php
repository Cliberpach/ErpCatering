@extends('layouts.layout')
@section('title-page')
    EDITAR COTIZACIÓN DE COMPRA
@endsection

@section('section-page')

@include('logistica.cotizacion_compra.modals.modal_productos')
@include('logistica.cotizacion_compra.modals.modal_edit_item')


<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos de la Cotización de Compra <i class="fa-solid fa-toolbox"></i></h6>
    </div>
    <div class="card-body">
        @include('logistica.cotizacion_compra.forms.form_edit_cotizacion_compra')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formActualizarCotizacionCompra">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>
    let dtProductos             =   null;
    let dtCompraDetalle         =   null;
    const lstCotizacionCompra   =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        cargarDetallePrevio();
        pintarTableCompraDetalle(lstCotizacionCompra);
        iniciarSelect2();
        iniciarDataTableProductos();
        iniciarDataTableCompraDetalle();
        events();
    })

    function events(){
        eventsMdlEditItem();

        document.querySelector('#formActualizarCotizacionCompra').addEventListener('submit',(e)=>{
            e.preventDefault();
            const validacion    =   validacionactualizarCotizacionCompra();
            if(validacion){
                actualizarCotizacionCompra();
            }
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('logistica.cotizacion_compra.index')}}';
                window.location.href    =   rutaIndex;
            }

            if (e.target.closest('.btnAgregarProducto')) {

                const inputCantidad =   document.querySelector('#cantidad'); 

                if(!inputCantidad.value){
                    toastr.error('INGRESE UNA CANTIDAD VÁLIDA');
                    inputCantidad.focus();
                    return;
                }

                if(inputCantidad.value === '0'){
                    toastr.error('INGRESE UNA CANTIDAD MAYOR A 0');
                    inputCantidad.focus();
                    return;
                }

                mostrarAnimacion1();
                agregarProducto({...producto_elegido},inputCantidad.value);
                ocultarAnimacion1();

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

    function iniciarDataTableProductos(){
        dtProductos  =   new DataTable('#table_productos',{
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

    function iniciarDataTableCompraDetalle(){
        dtCompraDetalle  =   new DataTable('#table_compra_detalle',{
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

    function cargarDetallePrevio() {
        const cotizacion_compra_detalle =   @json($cotizacion_compra_detalle);
        console.log(cotizacion_compra_detalle);
        cotizacion_compra_detalle.forEach((ccd)=>{
            lstCotizacionCompra.push(ccd);
        })
    }

    function validacionactualizarCotizacionCompra(){
        if(lstCotizacionCompra.length === 0){
            toastr.error('EL DETALLE DE LA COMPRA ESTÁ VACÍO!!!');
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
        limpiarTabla('table_compra_detalle');
        destruirDataTableCompraDetalle();
        pintarTableCompraDetalle(lstCotizacionCompra);
        iniciarDataTableCompraDetalle();
        toastr.info('PRODUCTO AGREGADO AL DETALLE');
    }

    function pintarTableCompraDetalle(lstItems){
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

        const tbody =   document.querySelector('#table_compra_detalle tbody');
        tbody.innerHTML =   filas;
    }

    async function getProductosByCategoria(categoria_id){
        try {
            mostrarAnimacion1();

            const token                         =   document.querySelector('input[name="_token"]').value;
            const urlGetProductosByCategoria    =   @json(route('logistica.cotizacion_compra.getProductosByCategoria', ['categoria_id' => 'CATEGORIA_ID']));
            const url                           =   urlGetProductosByCategoria.replace('CATEGORIA_ID', categoria_id);

            const response  =   await fetch(url, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                    });

            const   res =   await response.json();

            if(res.success){

                lstTableProductos.length = 0;
                res.productos.forEach((p)=>{
                    lstTableProductos.push(p);
                })

                destruirDataTableProductos();
                limpiarTabla('table_productos');
                pintarProductos(res.productos);
                iniciarDataTableProductos();
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR AL OBTENER PRODUCTOS');
            }

        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICION OBTENER PRODUCTOS');
        }finally{
            ocultarAnimacion1();
        }
    }

    function destruirDataTableProductos(){
        if(dtProductos){
            dtProductos.destroy();
            dtProductos =   null;
        }
    }

    function destruirDataTableCompraDetalle(){
        if(dtCompraDetalle){
            dtCompraDetalle.destroy();
            dtCompraDetalle =   null;
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


    function actualizarCotizacionCompra(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ACTUALIZAR LA COTIZACIÓN?",
        text: "Cotización de compra!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ACTUALIZAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError');
            const token                             =   document.querySelector('input[name="_token"]').value;
            const formActualizarCotizacionCompra    =   document.querySelector('#formActualizarCotizacionCompra');
            const formData                          =   new FormData();
            const id                                =   @json($id);
            let urlActualizarCotizacionCompra       =   `{{ route('logistica.cotizacion_compra.update', ['id' => ':id']) }}`;
            urlActualizarCotizacionCompra           =   urlActualizarCotizacionCompra.replace(':id', id);

            formData.append('lstCotizacionCompra',JSON.stringify(lstCotizacionCompra))

            Swal.fire({
                title: 'Cargando...',
                html: 'Actualizando cotización de compra...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlActualizarCotizacionCompra, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                            'X-HTTP-Method-Override': 'PUT' 
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
                    const cotizacion_compra_index     =   @json(route('logistica.cotizacion_compra.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   cotizacion_compra_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR COTIZACIÓN DE COMPRA');
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

</script>


