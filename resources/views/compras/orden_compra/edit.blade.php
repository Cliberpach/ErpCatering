@extends('layouts.layout')
@section('title-page')
    EDITAR ORDEN DE COMPRA
@endsection

@section('compras-collapsed', '')
@section('compras-expanded', 'true')
@section('compras-show', 'show')
@section('cotizacion_compra-active', 'active')

@section('section-page')

@include('compras.orden_compra.modals.modal_orden_compra_add_producto')
@include('compras.orden_compra.modals.modal_orden_compra_edit_producto')
@include('reutilizables.modals.proveedores.mdl_create_proveedor')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos de la Orden de Compra <i class="fa-solid fa-toolbox"></i></h6>
    </div>
    <div class="card-body">
        @include('compras.orden_compra.forms.form_edit_orden_compra')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formActualizarOrdenCompra">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>

    let dtProductos             =   null;
    let dtOrdenCompraDetalle    =   null;
    const lstOrdenCompra        =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        iniciarDataTableProductos();
        iniciarDataTableOrdenCompraDetalle();
        cargarDetallePrevio();
        //getTipoCambio();
        mostrarMsgErrors();
        
        const chkIgv    =   document.querySelector('#igv');
        const montos    =   calcularMontos(lstOrdenCompra,chkIgv.checked,chkIgv.value);
        const moneda    =   document.querySelector('#moneda').value;

        pintarTableMontos(montos,moneda);

        events();
    })

    function events(){
        eventsMdlEditItem();
        eventsMdlCreateProveedor();

        document.querySelector('#formActualizarOrdenCompra').addEventListener('submit',(e)=>{
            e.preventDefault();
            const validacion    =   validacionactualizarOrdenCompra();
            if(validacion){
                actualizarOrdenCompra();
            }
        })

        document.querySelector('#igv').addEventListener('change',(e)=>{
            toastr.clear();
            const estado    =   e.target.checked;
            const valorIgv  =   e.target.value;

            if(lstOrdenCompra.length > 0){
                const montos =  calcularMontos(lstOrdenCompra,estado,valorIgv);
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

    function iniciarDataTableOrdenCompraDetalle(){
        dtOrdenCompraDetalle  =   new DataTable('#table_orden_compra_detalle',{
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

    function validacionactualizarOrdenCompra(){
        if(lstOrdenCompra.length === 0){
            toastr.error('EL DETALLE DE LA ORDEN DE COMPRA ESTÁ VACÍO!!!');
            return false;
        }
        return true;
    }

    function agregarProducto(producto,cantidad){
        producto.cantidad       =   cantidad;
        producto.precio         =   document.querySelector('#precio').value;
        producto.total          =   parseFloat(producto.cantidad) * parseFloat(producto.precio);

        const indiceProducto    =   lstOrdenCompra.findIndex((p)=>{
            return p.producto_id == producto.producto_id;
        })

        if(indiceProducto !== -1){
            toastr.error('EL PRODUCTO YA EXISTE EN EL DETALLE');
            return;
        }

        lstOrdenCompra.push(producto);
        limpiarTabla('table_orden_compra_detalle');
        destruirDataTableOrdenCompraDetalle();
        pintarTableOrdenCompraDetalle(lstOrdenCompra);
        iniciarDataTableOrdenCompraDetalle();

        const inputIgv  =   document.querySelector('#igv');
        const montos    =   calcularMontos(lstOrdenCompra,inputIgv.checked,inputIgv.value);
        const moneda    =   document.querySelector('#moneda').value;

        pintarTableMontos(montos,moneda);
        toastr.info('PRODUCTO AGREGADO AL DETALLE');
    }

    function pintarTableOrdenCompraDetalle(lstItems){
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

        const tbody =   document.querySelector('#table_orden_compra_detalle tbody');
        tbody.innerHTML =   filas;
    }

    
    function destruirDataTableProductos(){
        if(dtProductos){
            dtProductos.destroy();
            dtProductos =   null;
        }
    }

    function destruirDataTableOrdenCompraDetalle(){
        if(dtOrdenCompraDetalle){
            dtOrdenCompraDetalle.destroy();
            dtOrdenCompraDetalle =   null;
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


    function actualizarOrdenCompra(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ACTUALIZAR LA ORDEN DE COMPRA?",
        text: "Se registrarán los cambios!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ACTUALIZAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            

            Swal.fire({
                title: 'Cargando...',
                html: 'Actualizando Orden de compra...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const token                             =   document.querySelector('input[name="_token"]').value;
                const formActualizarOrdenCompra         =   document.querySelector('#formActualizarOrdenCompra');
                const formData                          =   new FormData(formActualizarOrdenCompra);

                formData.append('lstOrdenCompra',JSON.stringify(lstOrdenCompra))
                const id                        =   @json($orden_compra->id);
                let urlActualizarOrdenCompra    =   `{{ route('compras.orden_compra.update', ['id' => ':id']) }}`;
                urlActualizarOrdenCompra        =   urlActualizarOrdenCompra.replace(':id', id);

             
                const response  =   await fetch(urlActualizarOrdenCompra, {
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
        const orden_compra_detalle      =   @json($orden_compra_detalle);
        const orden_compra              =   @json($orden_compra);

        if(orden_compra.moneda === 'PEN'){
            orden_compra_detalle.forEach((ocd)=>{
                const producto  =   {
                                        cantidad:ocd.cantidad,
                                        categoria_nombre:ocd.categoria_nombre,
                                        marca_nombre:ocd.marca_nombre,
                                        producto_id:ocd.producto_id,
                                        producto_nombre:ocd.producto_nombre,
                                        producto_unidad_medida:ocd.producto_unidad_medida,
                                        precio:ocd.precio_soles,
                                        total:parseFloat(ocd.cantidad) * parseFloat(ocd.precio_soles)
                                    }
                lstOrdenCompra.push(producto);
            })
        }
        
        

        pintarTableOrdenCompraDetalle(lstOrdenCompra);
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

        const chkIgv    =   document.querySelector('#igv');
        const montos    =   calcularMontos(lstOrdenCompra,chkIgv.checked,chkIgv.value);        
        pintarTableMontos(montos,moneda); 
        toastr.info('MONEDA ACTUALIZADA A'+' '+ moneda);  
    }

</script>


