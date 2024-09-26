@extends('layouts.layout')
@section('title-page')
    VER COMPRA
@endsection

@section('compras-collapsed', '')
@section('compras-expanded', 'true')
@section('compras-show', 'show')
@section('registro_compra-active', 'active')

@section('section-page')
<div class="container">
    <div class="card card-style settings-card-1 mb-30 border-primary shadow-lg rounded-lg">
        <!-- Encabezado de la tarjeta -->
        <div class="card-header bg-primary text-light d-flex justify-content-between align-items-center rounded-top" style="text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);">
            <h5 class="mb-0" style="color: white;">Detalles de la Compra</h5>
            <i class="fa-solid fa-box fa-2x"></i>
        </div>

        <!-- Cuerpo de la tarjeta -->
        <div class="card-body bg-light">
            <div class="row">

                <!-- Información de la Compra -->
                <div class="col-md-12">
                    <div class="border rounded p-4 bg-white">
                        <div class="row">
                            @php
                                $fields = [
                                    '#'                 =>  'RC-'.$compra->id,
                                    'Colaborador'       =>  $compra->colaborador_nombre,
                                    'Proveedor'         =>  $compra->proveedor_nombre,
                                    'Fecha Emisión'     =>  $compra->fecha_emision ?: 'No especificado',
                                    'Fecha Entrega'     =>  $compra->fecha_entrega ?: 'No especificado',
                                    'Documento'         =>  $compra->serie.'-'.$compra->correlativo,
                                    'Moneda'            =>  $compra->moneda,
                                    'Tipo de Cambio'    =>  number_format($compra->tipo_cambio, 4),
                                    'TIPO PRECIOS'      =>  $compra->precios_igv == 1 ? 'CON IGV' : 'SIN IGV',
                                    'IGV'               =>  number_format($compra->igv, 2),
                                    'Subtotal'          =>  number_format($compra->subtotal, 2),
                                    'Monto IGV'         =>  number_format($compra->monto_igv, 2),
                                    'Total'             =>  number_format($compra->total, 2),
                                    'Subtotal (S/)'     =>  number_format($compra->subtotal_soles, 2),
                                    'Monto IGV (S/)'    =>  number_format($compra->monto_igv_soles, 2),
                                    'Total (S/)'        =>  number_format($compra->total_soles, 2),
                                    'Observación'       =>  $compra->observacion ?: 'No hay observaciones',
                                    'Creado'            =>  $compra->fecha_registro ?: 'No especificado',
                                    'Actualizado'       =>  $compra->fecha_modificacion ?: 'No especificado'
                                ];
                            @endphp

                            @foreach ($fields as $label => $value)
                                <div class="col-lg-6 col-md-12 mb-3">
                                    <div class="d-block d-md-flex justify-content-between align-items-center p-2">
                                        <strong class="text-dark">{{ $label }}:</strong>
                                        @if ($label == 'Observación')
                                            <span class="text-muted" style="white-space: pre-wrap;">{{ $value }}</span>
                                        @else
                                            <span class="text-muted">{{ $value }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Tabla de Productos -->
                    <div class="col-12 mt-4">
                            <div class="border rounded p-4 bg-white">
                                <h6 class="text-primary mb-3 border-bottom pb-2">Lista de Productos</h6>
                                <div class="table-responsive">
                                <!-- Tabla responsiva -->
                                <table class="table table-hover table-bordered" id="tbl_compra_detalle">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Almacén</th>
                                            <th>Producto</th>
                                            <th>Precio S/</th>
                                            <th>Precio $</th>
                                            <th>Precio + IGV (S/)</th>
                                            <th>Precio + IGV ($)</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($productos as $index => $producto)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $producto->almacen_nombre }}</td>
                                            <td>{{ $producto->producto_nombre }}</td>
                                            <td>{{ number_format($producto->precio_soles, 2) }}</td>
                                            <td>{{ number_format($producto->precio_dolares, 2) }}</td>
                                            <td>{{ number_format($producto->precio_mas_igv_soles, 2) }}</td>
                                            <td>{{ number_format($producto->precio_mas_igv_dolares, 2) }}</td>
                                            <td>{{ number_format($producto->cantidad, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
               
            </div>
        </div>

        <!-- Pie de la tarjeta -->
        <div class="card-footer bg-primary text-white d-flex justify-content-end align-items-center">
            <div class="d-flex">
                <button class="btn btn-danger btnVolver me-2" type="button">
                    <i class="fa-solid fa-arrow-left"></i> VOLVER
                </button>
            </div>
        </div>
    </div>
</div>




@endsection


<script>
    let dtProductos         =   null;
    let dtCompraDetalle     =   null;
    const lstCompra         =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        iniciarDataTableCompraDetalle();
        events();
    })

    function events(){

        

      

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('compras.registro_compra.index')}}';
                window.location.href    =   rutaIndex;
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

   
    function iniciarDataTableCompraDetalle(){
        dtCompraDetalle  =   new DataTable('#tbl_compra_detalle',{
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
        let subtotal = 0, monto_igv = 0, total = 0;
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
    
    function validacionRegistrarCompra(){
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
        limpiarTabla('table_compra_detalle');
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
                            <td>${producto.almacen_nombre}</td>
                            <td>${producto.producto_unidad_medida}</td>
                            <td>${producto.precio}</td>
                            <td>${producto.cantidad}</td>
                            <td>${producto.total}</td>
                        </tr>`;
        })

        const tbody =   document.querySelector('#table_compra_detalle tbody');
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


    function registrarCompra(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR LA COMPRA?",
        text: "DOCUMENTO DE COMPRA!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError');
            const token                             =   document.querySelector('input[name="_token"]').value;
            const formRegistrarCompra               =   document.querySelector('#formRegistrarCompra');
            const formData                          =   new FormData(formRegistrarCompra);
            const urlRegistrarCompra                =   @json(route('compras.registro_compra.store'));

            formData.append('lstCompra',JSON.stringify(lstCompra))

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nueva compra...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarCompra, {
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
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR COMPRA');
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
</script>


