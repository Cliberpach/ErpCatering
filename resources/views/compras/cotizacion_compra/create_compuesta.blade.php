@extends('layouts.layout')
@section('title-page')
    REGISTRAR COTIZACIÓN DE COMPRA COMPUESTA
@endsection

@section('compras-collapsed', '')
@section('compras-expanded', 'true')
@section('compras-show', 'show')
@section('cotizacion_compra-active', 'active')

@section('section-page')

{{-- @include('compras.cotizacion_compra.modals.modal_productos')
@include('compras.cotizacion_compra.modals.modal_edit_item') --}}


<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos de la Cotización de Compra <i class="fa-solid fa-toolbox"></i></h6>
    </div>
    <div class="card-body">
        @include('compras.cotizacion_compra.forms.form_create_compuesta') 
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formRegistrarCotizacionCompuesta">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>
    let dtRequerimientos        =   null;
    let dtProductos             =   null;
    let dtCompraDetalle         =   null;
    let dtRequerimientoDetalle  =   null;

    const lstCotizacionCompra       =   [];
    let lstRequerimientoDetalle     =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        iniciarDataTableRequerimientos();      
        iniciarDataTableCompraDetalle();
        iniciarDataTableRequerimientoDetalle();
        events();
    })

    function events(){

        document.querySelector('#formRegistrarCotizacionCompuesta').addEventListener('submit',(e)=>{
            e.preventDefault();
            const validacion    =   validacionRegistrarCotizacionCompraCompuesta();
            if(validacion){
                registrarCotizacionCompraCompuesta();
            }
        })

        document.addEventListener('change',(e)=>{
            if(e.target.classList.contains('chkRequerimientoDetalle')){

                mostrarAnimacion1();
                const index     =   e.target.getAttribute('data-index');
                const   fila    =   lstRequerimientoDetalle[index];
                console.log(fila);

                toastr.clear();

                if(!fila){
                    toastr.danger('NO SE ENCONTRÓ LA FILA EN LA TABLA DETALLES DEL REQUERIMIENTO');
                    return;
                }

                if(e.target.checked){

                    //======= MARCADO - AGREGAR =====
                    agregarProducto(fila);

                }else{

                    //======== RETIRAR =======
                    console.log('DESMARCADO');
                    eliminarProducto(fila);

                }

                limpiarTabla('table_compra_detalle');
                destruirDataTableCompraDetalle();
                pintarTableCompraDetalle(lstCotizacionCompra);
                iniciarDataTableCompraDetalle();

                ocultarAnimacion1();
            }
        })

        document.addEventListener('click',(e)=>{

            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('compras.cotizacion_compra.index')}}';
                window.location.href    =   rutaIndex;
            }

            if(e.target.classList.contains('btnDeleteItem')){

                mostrarAnimacion1();
                toastr.clear();
                const index   =   e.target.getAttribute('data-id');
                
                if(!index){
                    toastr.error('NO EXISTE EL INDICE DEL DETALLE DE LA COTIZACIÓN');
                    return;
                }

                if(lstCotizacionCompra.length < index){
                    toastr.error('NO EXISTE EL INDICE DEL DETALLE DE LA COTIZACIÓN');
                    return; 
                }

                lstCotizacionCompra.splice(index,1);

                limpiarTabla('table_compra_detalle');
                destruirDataTableCompraDetalle();
                pintarTableCompraDetalle(lstCotizacionCompra);
                iniciarDataTableCompraDetalle();

                destruirDataTable(dtRequerimientoDetalle);
                limpiarTabla('table_compuesta_requerimiento_detalle');
                pintarTableRequerimientoDetalle(lstRequerimientoDetalle);
                iniciarDataTableRequerimientoDetalle();
                ocultarAnimacion1();

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


    function iniciarDataTableRequerimientos(){
        const urlGetRequerimientos = '{{ route('compras.cotizacion_compra.getRequerimientos') }}';

        dtRequerimientos  =   new DataTable('#table_compuesta_requerimientos',{
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
                { data: 'proyecto_nombre', name: 'proyecto_nombre' }
               
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).attr('data-id', data.id);
            },
            drawCallback: function() {
                $('#table_compuesta_requerimientos tbody tr').css('cursor', 'pointer');
                
                $('#table_compuesta_requerimientos tbody').off('click').on('click', 'tr', function () {
                    var dataId = $(this).data('id');
                    getRequerimientoDetalle(dataId);

                });
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

    function iniciarDataTableRequerimientoDetalle(){
        dtRequerimientoDetalle  =   new DataTable('#table_compuesta_requerimiento_detalle',{
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

    function validacionRegistrarCotizacionCompraCompuesta(){
        if(lstCotizacionCompra.length === 0){
            toastr.error('EL DETALLE DE LA COTIZACIÓN ESTÁ VACÍO!!!');
            return false;
        }
        return true;
    }

    function agregarProducto(producto_agregar){
        
        //========== COMPROBANDO SI EXISTE EL PRODUCTO EN EL LISTADO =====
        const existeProducto =   lstCotizacionCompra.findIndex((item)=>{
            return item.producto_id == producto_agregar.producto_id 
        })

        if(existeProducto !== -1){

            //====== EN CASO YA EXISTA EL PRODUCTO, COMPROBAR SI EXISTE EL REQUERIMIENTO =====
            const indiceExisteRequerimiento   = lstCotizacionCompra[existeProducto].requerimientos.findIndex((requerimiento_id)=>{
                return requerimiento_id == producto_agregar.requerimiento_id;
            })


            if(indiceExisteRequerimiento !== -1){
                //====== EN CASO YA EXISTA ======
                toastr.danger('YA EXISTE EL PRODUCTO DE ESTE REQUERIMIENTO EN EL DETALLE!!');
                return;
            }else{

                console.log('indice existe req',indiceExisteRequerimiento);
                //======== EN CASO EL PRODUCTO SEA DE UN REQUERIMIENTO NUEVO, SUMAR CANTIDAD ======
                lstCotizacionCompra[existeProducto].cantidad +=  parseFloat(producto_agregar.cantidad);
                lstCotizacionCompra[existeProducto].requerimientos.push(producto_agregar.requerimiento_id);
            
                toastr.success('PRODUCTO AGREGADO!!!','CANTIDAD ACUMULADA');
            }

        }else{

            //======= EL PRODUCTO ES NUEVO =======
            const producto_nuevo    =   {   producto_id:producto_agregar.producto_id,
                                            producto_nombre:producto_agregar.producto_nombre,
                                            producto_unidad_medida:producto_agregar.producto_unidad_medida,
                                            marca_nombre:producto_agregar.marca_nombre,
                                            categoria_nombre:producto_agregar.categoria_nombre,
                                            cantidad:parseFloat(producto_agregar.cantidad),
                                            requerimientos:[producto_agregar.requerimiento_id]
                                        }

            lstCotizacionCompra.push(producto_nuevo);

            toastr.success('PRODUCTO NUEVO AGREGADO!!!');
        }


    }

    function eliminarProducto(producto_eliminar){
           
        //======= COMPROBAR SI EXISTE EL PRODUCTO Y EL REQUERIMIENTO =======
        const indiceExisteProducto =   lstCotizacionCompra.findIndex((item)=>{
                return item.producto_id ==   producto_eliminar.producto_id;
        })

        console.log('INDICE EXISTE PRODUCTO');
        console.log(indiceExisteProducto);

        if(indiceExisteProducto === -1){

            toastr.danger('NO EXISTE EL PRODUCTO EN EL DETALLE DE LA COTIZACIÓN!!!');
            return;

        }else{

            //========= COMPROBAR SI EXISTE EL REQUERIMIENTO ========
            const indiceExisteRequerimiento =   lstCotizacionCompra[indiceExisteProducto].requerimientos.findIndex((requerimiento_id)=>{
                return requerimiento_id == producto_eliminar.requerimiento_id;
            })

            if(indiceExisteRequerimiento === -1){
                toastr.danger('NO EXISTE EL REQUERIMIENTO EN EL DETALLE DE LA COTIZACIÓN!!!');
                return;
            }else{

                //========= ELIMINANDO REQUERIMIENTO ========
                lstCotizacionCompra[indiceExisteProducto].requerimientos.splice(indiceExisteRequerimiento,1);
                lstCotizacionCompra[indiceExisteProducto].cantidad  -=  parseFloat(producto_eliminar.cantidad);

                //====== EN CASO SE QUEDE SIN REQUERIMIENTOS, ELIMINAR PRODUCTO =======
                const cantidad_requerimientos = lstCotizacionCompra[indiceExisteProducto].requerimientos.length;
                cantidad_requerimientos === 0?lstCotizacionCompra.splice(indiceExisteProducto,1):null;

            }
        }

    }

    function pintarTableCompraDetalle(lstItems){
        let filas   =   ``;
        lstItems.forEach((producto,index)=>{
            filas   +=  `<tr>
                            <th>
                                <div style="display:flex;justify-content:center;gap:5px;">
                                    <i class="fas fa-trash-alt btn btn-danger btnDeleteItem" data-id="${index}"></i>
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


    function registrarCotizacionCompraCompuesta(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR LA COTIZACIÓN COMPUESTA?",
        text: "Se marcarán los requerimientos con esta cotización!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError');
            const token                                     =   document.querySelector('input[name="_token"]').value;
            const formRegistrarCotizacionCompuesta          =   document.querySelector('#formRegistrarCotizacionCompuesta');
            const formData                                  =   new FormData();
            const urlRegistrarCotizacionCompraCompuesta     =   @json(route('compras.cotizacion_compra.storeCompuesta'));

            formData.append('lstCotizacionCompra',JSON.stringify(lstCotizacionCompra));
            formData.append('proyecto_id',@json($proyecto->proyecto_id));
            formData.append('colaborador_registrador_id',@json($colaborador_registrador->colaborador_id));


            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nueva cotización de compra...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarCotizacionCompraCompuesta, {
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
                    const cotizacion_compra_index     =   @json(route('compras.cotizacion_compra.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   cotizacion_compra_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR PRODUCTO');
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

    async function getRequerimientoDetalle(requerimiento_id){
        mostrarAnimacion1();
        try {
            lstRequerimientoDetalle             =   [];
            toastr.clear();
            const token                         =   document.querySelector('input[name="_token"]').value;
            const urlGetRequerimientoDetalle    =   '{{ route("compras.cotizacion_compra.getRequerimientoDetalle", ":requerimiento_id") }}'.replace(':requerimiento_id', requerimiento_id);

            const response  =   await fetch(urlGetRequerimientoDetalle, {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': token 
                                    },
                                });

            const   res =   await response.json();

            if(res.success){
                destruirDataTable(dtRequerimientoDetalle);
                limpiarTabla('table_compuesta_requerimiento_detalle');
                pintarTableRequerimientoDetalle(res.requerimiento_detalle);
                iniciarDataTableRequerimientoDetalle();
                lstRequerimientoDetalle =   res.requerimiento_detalle;
                toastr.info('DETALLE OBTENIDO');
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR AL OBTENER TIPO DE CAMBIO');
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN AL OBTENER TIPO DE CAMBIO');
        }finally{
            ocultarAnimacion1();
        }
    }

    function pintarTableRequerimientoDetalle(lstItems){
        const tbody =   document.querySelector('#table_compuesta_requerimiento_detalle tbody');
        let filas   =   ``;

        lstItems.forEach((item,index)=>{

            let marcado =   '';

            //========= COMPROBAR SI ESTE PRODUCTO Y REQ YA FUE AGREGADO AL DETALLE DE LA COTIZACIÓN =======
            const indiceExisteProducto  =   lstCotizacionCompra.findIndex((producto)=>{
                return producto.producto_id == item.producto_id;
            })

            if(indiceExisteProducto !== -1){
                const indiceExisteRequerimiento =   lstCotizacionCompra[indiceExisteProducto].requerimientos.findIndex((requerimiento_id)=>{
                    return requerimiento_id == item.requerimiento_id;
                })

                if(indiceExisteRequerimiento !== -1){
                    marcado =   'checked';
                }
            }

            filas   +=  `<tr>
                            <td>
                                <input ${marcado} type="checkbox" class="form-check-input chkRequerimientoDetalle" data-index="${index}">    
                            </td>
                            <td>
                                ${item.producto_nombre}
                            </td>
                             <td>
                                ${item.cantidad}
                            </td>
                        </tr>`;
        })

        tbody.innerHTML =   filas;
    }

</script>


