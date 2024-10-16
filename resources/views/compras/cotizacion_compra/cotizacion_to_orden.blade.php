@extends('layouts.layout')
@section('title-page')
    CONVERTIR COTIZACIÓN A ORDEN DE COMPRA
@endsection

@section('compras-collapsed', '')
@section('compras-expanded', 'true')
@section('compras-show', 'show')
@section('cotizacion_compra-active', 'active')

@section('section-page')

@include('compras.cotizacion_compra.modals.modal_productos_cotizacion_to_orden')
@include('compras.cotizacion_compra.modals.modal_edit_item_cotizacion_to_orden')
@include('reutilizables.modals.proveedores.mdl_create_proveedor')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos de la Orden de Compra <i class="fa-solid fa-toolbox"></i></h6>
    </div>
    <div class="card-body">
        @include('compras.cotizacion_compra.forms.form_cotizacion_to_orden')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formCotizacionToOrden">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>

    let dtProductos         =   null;
    let dtCompraDetalle     =   null;
    const lstCotizacionCompra  =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        iniciarDataTableProductos();
        iniciarDataTableCotizacionToOrdenDetalle();
        cargarDetallePrevio();
        getTipoCambio();
        mostrarMsgErrors();
        
        const montos    =   calcularMontos(lstCotizacionCompra,true,18);
        const moneda    =   document.querySelector('#moneda').value;

        pintarTableMontos(montos,moneda);

        events();
    })

    function events(){
        eventsMdlEditItem();
        eventsMdlCreateProveedor();

        document.querySelector('#formCotizacionToOrden').addEventListener('submit',(e)=>{
            e.preventDefault();
            const validacion    =   validacionRegistrarCotizacionToOrden();
            if(validacion){
                registrarCotizacionToOrden();
            }
        })

        document.querySelector('#igv').addEventListener('change',(e)=>{
            toastr.clear();
            const estado    =   e.target.checked;
            const valorIgv  =   e.target.value;

            if(lstCotizacionCompra.length > 0){
                const montos =  calcularMontos(lstCotizacionCompra,estado,valorIgv);
                const moneda    =   document.querySelector('#moneda').value;

                pintarTableMontos(montos,moneda);
                toastr.info('MONTOS ACTUALIZADOS');
            }
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('compras.cotizacion_compra.index')}}';
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

    function iniciarDataTableCotizacionToOrdenDetalle(){
        dtCompraDetalle  =   new DataTable('#table_cotizacion_to_orden_detalle',{
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
        const inputPrecio   =   document.querySelector('#precio'); 

        if(!inputCantidad.value){
            toastr.error('DEBE INGRESAR UNA CANTIDAD!!');
            return false;
        }
        if(inputCantidad.value == 0){
            toastr.error('LA CANTIDAD DEBE SER MAYOR A 0!!');
            return false;
        }

        if(!inputPrecio.value){
            inputPrecio.focus();
            toastr.error('DEBE INGRESAR UN PRECIO!!');
            return false;
        }

        return true;
    }

    function validacionRegistrarCotizacionToOrden(){
        if(lstCotizacionCompra.length === 0){
            toastr.error('EL DETALLE DE LA COMPRA ESTÁ VACÍO!!!');
            return false;
        }
        return true;
    }

    function agregarProducto(producto,cantidad){
        producto.cantidad       =   cantidad;
        producto.precio         =   document.querySelector('#precio').value;
        producto.total          =   parseFloat(producto.cantidad) * parseFloat(producto.precio);

        const indiceProducto    =   lstCotizacionCompra.findIndex((p)=>{
            return p.producto_id == producto.producto_id;
        })

        if(indiceProducto !== -1){
            toastr.error('EL PRODUCTO YA EXISTE EN EL DETALLE');
            return;
        }

        lstCotizacionCompra.push(producto);
        limpiarTabla('table_cotizacion_to_orden_detalle');
        destruirDataTableCompraDetalle();
        pintarTableCompraDetalle(lstCotizacionCompra);
        iniciarDataTableCotizacionToOrdenDetalle();

        const inputIgv  =   document.querySelector('#igv');
        const montos    =   calcularMontos(lstCotizacionCompra,inputIgv.checked,inputIgv.value);
        const moneda    =   document.querySelector('#moneda').value;

        pintarTableMontos(montos,moneda);
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
                            <td>${producto.precio}</td>
                            <td>${producto.cantidad}</td>
                            <td>${producto.total}</td>
                        </tr>`;
        })

        const tbody =   document.querySelector('#table_cotizacion_to_orden_detalle tbody');
        tbody.innerHTML =   filas;
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


    function registrarCotizacionToOrden(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA GENERAR LA ORDEN DE COMPRA?",
        text: "Se convertirá la cotización de compra a orden de compra!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, GENERAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            const token                             =   document.querySelector('input[name="_token"]').value;
            const formCotizacionToOrden             =   document.querySelector('#formCotizacionToOrden');
            const formData                          =   new FormData(formCotizacionToOrden);
            const urlRegistrarCotizacionCompra      =   @json(route('compras.cotizacion_compra.cotizacionToOrden'));

            formData.append('lstCotizacionCompra',JSON.stringify(lstCotizacionCompra))
            formData.append('cotizacion_compra_id',@json($cotizacion_compra->id))
            formData.append('requerimiento_id', {{ $requerimiento->id ?? 'null' }});

            Swal.fire({
                title: 'Cargando...',
                html: 'Convirtiendo cotización a orden de compra...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarCotizacionCompra, {
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
                    const orden_compra_index     =   @json(route('compras.orden_compra.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   orden_compra_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN GENERAR ORDEN DE COMPRA');
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
        const cotizacion_detalle    =   @json($cotizacion_compra_detalle);
        
        cotizacion_detalle.forEach((cd)=>{
            const producto  =   {
                                    cantidad:cd.cantidad,
                                    categoria_nombre:cd.categoria_nombre,
                                    marca_nombre:cd.marca_nombre,
                                    producto_id:cd.producto_id,
                                    producto_nombre:cd.producto_nombre,
                                    producto_unidad_medida:cd.producto_unidad_medida,
                                    precio:cd.precio,
                                    total:parseFloat(cd.cantidad) * parseFloat(cd.precio)
                                }
            lstCotizacionCompra.push(producto);
        })

        pintarTableCompraDetalle(lstCotizacionCompra);
    }

    async function getTipoCambio(){
        mostrarAnimacion1();
        try {
            document.querySelector('#tipo_cambio').value        =   '';
            document.querySelector('#tipo_cambio').readOnly     =   true;
            document.querySelector('#lbl_tipo_cambio').classList.remove('required_field');

         

            const token             =   document.querySelector('input[name="_token"]').value;
            const urlGetTipoCambio  =   @json(route('utils.tipoCambio'));

            const response  =   await fetch(urlGetTipoCambio, {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': token 
                                    },
                                });

            const   res =   await response.json();

            if(res.success){
                setTipoCambio(res.data);
                document.querySelector('#tipo_cambio').readOnly     =   false;
                document.querySelector('#lbl_tipo_cambio').classList.add('required_field');
                toastr.info('TIPO CAMBIO OBTENIDO');
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR AL OBTENER TIPO DE CAMBIO');
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN AL OBTENER TIPO DE CAMBIO');
        }finally{
            ocultarAnimacion1();
        }
    }

    function setTipoCambio(data){
        const inputTipoCambio   =   document.querySelector('#tipo_cambio');
        inputTipoCambio.value   =   data.venta;
    }

    function calcularMontos(lstItems,chkIgv,valorIgv){
        let subtotal    =   0;
        let monto_igv   =   0;
        let total       =   0;
        valorIgv    =   parseFloat(valorIgv);

        if(chkIgv){ //======= PRECIOS CON IGV ======
            
            lstItems.forEach((item)=>{
                total   +=  parseFloat(item.total);
            })

            subtotal    =   total/((100 + valorIgv)/100);
            monto_igv   =   total - subtotal;
        }else{

            //======= PRECIOS SIN IGV =======
            lstItems.forEach((item)=>{
                subtotal   +=  item.total;
            })

            monto_igv   =   (valorIgv/100)*subtotal;
            total       =   subtotal + monto_igv;
        }

        return {subtotal,monto_igv,total};
    }

    function pintarTableMontos(montos,moneda){
        toastr.clear();
        if(moneda != 'PEN' && moneda != 'USD'){
            toastr.error('EL FORMATO DE MONEDA ES INCORRECTO!!!');
            return;
        }

        const tdSubtotal    =   document.querySelector('#tbl_subtotal');
        const tdMontoIgv    =   document.querySelector('#tbl_monto_igv');
        const tdTotal       =   document.querySelector('#tbl_total');
        let region          =   '';

        if(moneda == 'PEN'){
            region  =   'es-PE';  
        }

        if(moneda == 'USD'){
            region  =   'en-US';  
        }

        tdSubtotal.textContent  = formatCurrency(montos.subtotal,region,moneda);
        tdMontoIgv.textContent  = formatCurrency(montos.monto_igv,region,moneda);
        tdTotal.textContent     = formatCurrency(montos.total,region,moneda);

    }

    function formatCurrency(amount,region,moneda) {
        return new Intl.NumberFormat(region, {
            style: 'currency',
            currency: moneda,
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }

    function changeMoneda(moneda){
        toastr.clear();

        if(moneda != 'PEN' && moneda != 'USD'){
            toastr.error('EL FORMATO DE MONEDA ES INCORRECTO!!!');
            return;
        }

        const montos =  calcularMontos(lstCotizacionCompra,false,18);
        pintarTableMontos(montos,moneda); 
        toastr.info('MONEDA ACTUALIZADA A'+' '+ moneda);  
    }

</script>


