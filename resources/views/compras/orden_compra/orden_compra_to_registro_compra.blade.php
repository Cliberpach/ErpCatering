@extends('layouts.layout')
@section('title-page')
    GENERAR REGISTRO DE COMPRA
@endsection

@section('compras-collapsed', '')
@section('compras-expanded', 'true')
@section('compras-show', 'show')
@section('orden_compra-active', 'active')

@section('section-page')

@include('compras.registro_compra.modals.modal_productos')
@include('compras.registro_compra.modals.modal_edit_item')
@include('reutilizables.modals.proveedores.mdl_create_proveedor')


<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos del registro de compra <i class="fa-solid fa-toolbox"></i></h6>
    </div>
    <div class="card-body">
        @include('compras.orden_compra.forms.form_orden_compra_to_registro_compra')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formOrdenCompraToRegistroCompra">
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
    const lstCompra         =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        
        iniciarDataTableProductos();
        iniciarDataTableCompraDetalle();
        //getTipoCambio();
        cargarProductosPrevios();
        iniciarSelect2();

       

        events();
    })

    function events(){
        eventsMdlEditItem();
        eventsMdlCreateProveedor();

        document.querySelector('#formOrdenCompraToRegistroCompra').addEventListener('submit',(e)=>{
            e.preventDefault();
            const validacion    =   validacionordenCompraToRegistroCompra();
            if(validacion){
                ordenCompraToRegistroCompra();
            }
        })

        // document.querySelector('#igv').addEventListener('change',(e)=>{
        //     toastr.clear();
        //     const estado    =   e.target.checked;
        //     const valorIgv  =   e.target.value;

        //     if(lstCompra.length > 0){
        //         const montos =  calcularMontos(lstCompra,estado,valorIgv);
        //         pintarTableMontos(montos);
        //         toastr.info('MONTOS ACTUALIZADOS');
        //     }
        // })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('compras.registro_compra.index')}}';
                window.location.href    =   rutaIndex;
            }

            if (e.target.closest('.btnAgregarProducto')) {
                toastr.clear();
                const validacion    =   validacionAgregarProducto();

                if(validacion){
                    mostrarAnimacion1();
                    agregarProducto({...producto_elegido});
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

    

    function iniciarDataTableProductos(){
        const urlGetProductos   =   @json(route('registros.producto.getProductos'));
        
        dtProductos  =   new DataTable('#table_productos',{
            serverSide: true,  
            processing: true,  
            pageLength: 50, 

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

    function iniciarDataTableCompraDetalle(){
        dtCompraDetalle  =   new DataTable('#table_orden_compra_to_registro_compra',{
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
        const selectAlmacen =   document.querySelector('#almacen');

        if(!inputCantidad.value){
            inputCantidad.focus();
            toastr.error('DEBE INGRESAR UNA CANTIDAD!!');
            return false;
        }
        if(inputCantidad.value == 0){
            inputCantidad.focus();
            toastr.error('LA CANTIDAD DEBE SER MAYOR A 0!!');
            return false;
        }

        if(!inputPrecio.value){
            inputPrecio.focus();
            toastr.error('DEBE INGRESAR UN PRECIO!!');
            return false;
        }

        if(!selectAlmacen.value){
            selectAlmacen.focus();
            toastr.error('DEBE SELECCIONAR UN ALMACÉN!!');
            return false;
        }
        // if(inputPrecio == 0){
        //     toastr.error('EL PRECIO DEBE SER MAYOR A 0!');
        //     return false;
        // }

        return true;
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
    
    function validacionordenCompraToRegistroCompra(){
        if(lstCompra.length === 0){
            toastr.error('EL DETALLE DE LA COMPRA ESTÁ VACÍO!!!');
            return false;
        }
        return true;
    }

    function agregarProducto(producto){
        producto.cantidad       =   document.querySelector('#cantidad').value;
        producto.precio         =   document.querySelector('#precio').value;
        producto.total          =   parseFloat(producto.cantidad) * parseFloat(producto.precio);
        producto.almacen_id     =   document.querySelector('#almacen').value;
        producto.almacen_nombre =   document.querySelector('#almacen').options[document.querySelector('#almacen').selectedIndex].textContent;

        const indiceProducto    =   lstCompra.findIndex((p)=>{
            return p.producto_id == producto.producto_id;
        })

        if(indiceProducto !== -1){
            toastr.error('EL PRODUCTO YA EXISTE EN EL DETALLE');
            return;
        }

        lstCompra.push(producto);
        limpiarTabla('table_orden_compra_to_registro_compra');
        destruirDataTableCompraDetalle();
        pintarTableCompraDetalle(lstCompra);
        iniciarDataTableCompraDetalle();

        const inputIgv  =   document.querySelector('#igv');
        const montos    =   calcularMontos(lstCompra,inputIgv.checked,inputIgv.value);
        pintarTableMontos(montos);
        toastr.info('PRODUCTO AGREGADO AL DETALLE');
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }

    function pintarTableMontos(montos){
        const tdSubtotal    =   document.querySelector('#tbl_subtotal');
        const tdMontoIgv    =   document.querySelector('#tbl_monto_igv');
        const tdTotal       =   document.querySelector('#tbl_total');

        tdSubtotal.textContent  = formatCurrency(montos.subtotal);
        tdMontoIgv.textContent  = formatCurrency(montos.monto_igv);
        tdTotal.textContent     = formatCurrency(montos.total);
    }

    function cargarProductosPrevios(){
        const orden_compra_detalle  =   @json($orden_compra_detalle);
        const orden_compra          =   @json($orden_compra);

        orden_compra_detalle.forEach((ocd)=>{

            const item                  =   {};
            item.producto_id            =   ocd.producto_id;
            item.producto_nombre        =   ocd.producto_nombre;
            item.categoria_nombre       =   ocd.categoria_nombre;
            item.marca_nombre           =   ocd.marca_nombre;  
            item.almacen_nombre         =   'CENTRAL';
            item.almacen_id             =   1;
            item.producto_unidad_medida =   ocd.producto_unidad_medida;
            
            if(orden_compra.moneda === 'PEN'){
                item.precio             =   Number(ocd.precio_soles).toFixed(2);
                item.cantidad           =   Number(ocd.cantidad).toFixed(2);
                item.total              =   parseFloat(ocd.precio_soles)*parseFloat(ocd.cantidad);
                item.total              =   Number(item.total).toFixed(2);
            }

            if(orden_compra.moneda === 'USD'){
                item.precio             =   Number(ocd.precio_dolares).toFixed(2);
                item.cantidad           =   Number(ocd.cantidad).toFixed(2);
                item.total              =   parseFloat(ocd.precio_dolares)*parseFloat(ocd.cantidad);
                item.total              =   Number(item.total).toFixed(2);
            }

            lstCompra.push(item);

        })

        limpiarTabla('table_orden_compra_to_registro_compra');
        destruirDataTableCompraDetalle();
        pintarTableCompraDetalle(lstCompra);
        iniciarDataTableCompraDetalle();

    }

    function pintarTableCompraDetalle(lstItems){
        let filas       =   ``;
        const almacenes =   @json($almacenes);
        
        let opcionesAlmacen = '';
        almacenes.forEach(almacen => {
            opcionesAlmacen += `<option value="${almacen.id}">${almacen.descripcion}</option>`;
        });

        lstItems.forEach((producto,index)=>{
            filas   +=  `<tr>
                            <th>
                                <div style="display:flex;justify-content:center;gap:5px;">
                                    <div class="input-group mb-3">
                                        <select data-id="${index}" onchange="setAlmacenItemCompra(this)" name="almacen" data-placeholder="Seleccionar" class="almacenItemCompra select2_form">
                                            ${opcionesAlmacen}
                                        </select>
                                    </div>
                                </div>
                            </th>
                            <td>${producto.producto_nombre}</td>
                            <td>${producto.categoria_nombre}</td>
                            <td>${producto.marca_nombre}</td>
                            <td>${producto.producto_unidad_medida}</td>
                            <td>${producto.precio}</td>
                            <td>${producto.cantidad}</td>
                            <td>${Number(producto.total).toFixed(2)}</td>
                        </tr>`;
        })


        const tbody =   document.querySelector('#table_orden_compra_to_registro_compra tbody');
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


    function ordenCompraToRegistroCompra(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA GENERAR EL REGISTRO DE COMPRA?",
        text: "DOCUMENTO DE COMPRA!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, GENERAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            const token                             =   document.querySelector('input[name="_token"]').value;
            const formOrdenCompraToRegistroCompra   =   document.querySelector('#formOrdenCompraToRegistroCompra');
            const formData                          =   new FormData(formOrdenCompraToRegistroCompra);
            const urlOrdenCompraToRegistrarCompra   =   @json(route('compras.orden_compra.ordenCompraToRegistroCompra'));

            formData.append('lstCompra',JSON.stringify(lstCompra));
            formData.append('orden_compra_id',@json($orden_compra->id));

            Swal.fire({
                title: 'Cargando...',
                html: 'Generando registro de compra...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlOrdenCompraToRegistrarCompra, {
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
                    const compra_index      =   @json(route('compras.registro_compra.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   compra_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN GENERAR REGISTRO DE COMPRA');
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

    function pintarErroresValidacion(objErroresValidacion) {
        toastr.clear();

        let erroresLstCompra = '';

        for (let clave in objErroresValidacion) {
            if (clave.startsWith('lstCompra')) {
                const mensaje = objErroresValidacion[clave][0];
                    
                const partes = clave.split('.');
                const index = partes[1];
                    
                erroresLstCompra += `->${mensaje}<br>`;
            } else {
                const pError = document.querySelector(`.${clave}_error`);
                if (pError) {
                    pError.textContent = objErroresValidacion[clave][0];
                }
            }
        }

        if (erroresLstCompra) {
            toastr.options = {
                escapeHtml: false, 
                timeOut: 0,
                extendedTimeOut: 0,
                onShown: function() {
                    const toastrElement = document.querySelector('.toast');
                    if (toastrElement) {
                        toastrElement.style.opacity = '1'; 
                    }
                }
            };
            toastr.error(erroresLstCompra, 'ERROR DE VALIDACIÓN');
        }
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

    function setAlmacenItemCompra(selectAlmacenItemCompra){

        const selectAlmacenItemCompraIndex  =   selectAlmacenItemCompra.selectedIndex;
        const index                         =   selectAlmacenItemCompra.getAttribute('data-id');

        let almacen_nombre                  =   null;
        let almacen_id                      =   null;

        if(selectAlmacenItemCompraIndex != -1){
            almacen_nombre        =   selectAlmacenItemCompra.options[selectAlmacenItemCompra.selectedIndex].text;
            almacen_id            =   selectAlmacenItemCompra.value;
        }

        lstCompra[index].almacen_id     =   almacen_id;
        lstCompra[index].almacen_nombre =   almacen_nombre;

    }
</script>


