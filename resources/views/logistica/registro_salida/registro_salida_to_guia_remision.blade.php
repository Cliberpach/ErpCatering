@extends('layouts.layout')
@section('title-page')
<div class="row">
    <div class="col-12 d-flex align-items-center">
        <img width="46" height="46" src="{{asset('img/icons/documento/documento1.png')}}" alt="coins"/> GENERAR GUÍA DE REMISIÓN
    </div>
</div>
@endsection

@section('logistica-collapsed', '')
@section('logistica-expanded', 'true')
@section('logistica-show', 'show')
@section('registro_salida-active', 'active')

@section('section-page')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-5  d-flex justify-content-between align-items-center">
      <h6>Datos de la Guía de Remisión</h6>
    </div>
    <div class="card-body">
        @include('logistica.registro_salida.forms.form_registro_salida_to_guia_remision') 
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formRegistroSalidaToGuiaRemision">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>
    let dtProductos         =   null;
    let dtGuiaDetalle     =   null;
    const lstGuiaRemision   =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        iniciarDataTableGuiaDetalle();
        cargarDetalleGuia()        
        events();
    })

    function events(){
        document.querySelector('#formRegistroSalidaToGuiaRemision').addEventListener('submit',(e)=>{
            e.preventDefault();
            const validacion    =   validacionRegistrarGuiaRemision();
            if(validacion){
                registrarGuiaRemision();
            }
        })

        document.addEventListener('input',(e)=>{

            if(e.target.classList.contains('cantidad')){
                const select_almacen    =   document.querySelector('#almacen_origen');
                const cantidad          =   e.target.value;
                const type              =   e.target.getAttribute('data-type');
                const producto_id       =   producto_elegido.producto_id;
                const inputCantidad     =   document.querySelector('#cantidad')

                toastr.clear();
                if(!select_almacen.value){
                    toastr.error('DEBE SELECCIONAR UN ALMACÉN DE ORIGEN!!!');
                    select_almacen.focus();
                    return;
                }
                if(!producto_id){
                    toastr.error('DEBE SELECCIONAR UN PRODUCTO!!!');
                    document.querySelector('.btnBuscarProducto').focus();
                    return;
                }
                if(!cantidad){
                    return;
                }

                validarCantidad(select_almacen.value,producto_id,cantidad,inputCantidad);
            }
        })

        // document.querySelector('#cantidad').addEventListener('input',(e)=>{
           

        // })
     

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('logistica.registro_salida.index')}}';
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
            placeholder: $( this ).data( 'placeholder' ),
            allowClear: true,
            //width: '100%'
        } );
        $('.select2-selection--single').css('background-color', 'rgb(253, 253, 215)');
    }

    function iniciarDataTableProductos(){
        const urlGetProductos   =   @json(route('logistica.producto.getProductosByAlmacen'));
        
        dtProductos  =   new DataTable('#table_productos',{
            serverSide: true,  
            processing: true,  
            ajax: {
                url: urlGetProductos, 
                type: 'GET',  
                data: function(d) {
                    d.categoria_id  =   $('#categoria').val();  
                    d.marca_id      =   $('#marca').val();  
                    d.almacen_id    =   $('#almacen_origen').val();  
                },
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'marca_nombre', name: 'marca_nombre' },
                { data: 'categoria_nombre', name: 'categoria_nombre' },
                { data: 'stock', name: 'stock' }
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

    function iniciarDataTableGuiaDetalle(){
        dtGuiaDetalle  =   new DataTable('#table_registro_salida_to_guia_detalle',{
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
            inputCantidad.focus();
            toastr.error('DEBE INGRESAR UNA CANTIDAD!!');
            return false;
        }
        if(inputCantidad.value == 0){
            inputCantidad.focus();
            toastr.error('LA CANTIDAD DEBE SER MAYOR A 0!!');
            return false;
        }

        return true;
    }

    function validacionRegistrarGuiaRemision(){
        if(lstGuiaRemision.length === 0){
            toastr.error('EL DETALLE DE LA SALIDA ESTÁ VACÍO!!!');
            return false;
        }
        return true;
    }

   

   

    function pintarTableGuiaDetalle(lstItems){
        let filas   =   ``;
        lstItems.forEach((producto,index)=>{
            filas   +=  `<tr>
                            <th>
                               ${index + 1}
                            </th>
                            <td>${producto.producto_nombre}</td>
                            <td>${producto.categoria_nombre}</td>
                            <td>${producto.marca_nombre}</td>
                            <td>${producto.producto_unidad_medida}</td>
                            <td>${producto.cantidad}</td>
                          
                        </tr>`;
        })

        const tbody =   document.querySelector('#table_registro_salida_to_guia_detalle tbody');
        tbody.innerHTML =   filas;
    }

    async function getProductosByAlmacen(almacen_id){
        try {
            mostrarAnimacion1();

            const token                         =   document.querySelector('input[name="_token"]').value;
            const urlGetProductosByAlmacen      =   @json(route('registros.producto.getProductosByAlmacen', ['almacen_id' => ':almacen_id']));
            const url                           =   urlGetProductosByAlmacen.replace(':almacen_id', almacen_id);

            const response  =   await fetch(url, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                    });

            const   res =   await response.json();
            console.log(res);
            if(res.success){

                // lstTableProductos.length = 0;
                // res.productos.forEach((p)=>{
                //     lstTableProductos.push(p);
                // })

                // destruirDataTableProductos();
                // limpiarTabla('table_productos');
                // pintarProductos(res.productos);
                // iniciarDataTableProductos();
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR AL OBTENER PRODUCTOS POR ALMACÉN');
            }

        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICION OBTENER PRODUCTOS POR ALMACÉN');
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

    function destruirDataTableSalidaDetalle(){
        if(dtGuiaDetalle){
            dtGuiaDetalle.destroy();
            dtGuiaDetalle =   null;
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


    function registrarGuiaRemision(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR LA GUÍA DE REMISIÓN?",
        text: "OPERACIÓN NO REVERSIBLE!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            const token                             =   document.querySelector('input[name="_token"]').value;
            const formRegistroSalidaToGuiaRemision               =   document.querySelector('#formRegistroSalidaToGuiaRemision');
            const formData                          =   new FormData(formRegistroSalidaToGuiaRemision);
            const urlRegistrarSalida                =   @json(route('logistica.registro_salida.store'));

            formData.append('lstGuiaRemision',JSON.stringify(lstGuiaRemision))

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nueva salida...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarSalida, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                        body: formData
                                    });

                const   res =   await response.json();
                                
                if(response.status === 422){
                    if('errors' in res){
                        pintarErroresValidacion(res.errors);
                    }
                    Swal.close();
                    return;
                }
                
                if(res.success){
                    const salida_index      =   @json(route('logistica.registro_salida.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   salida_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR SALIDA');
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

        let erroreslstGuiaRemision = '';

        for (let clave in objErroresValidacion) {
            if (clave.startsWith('lstGuiaRemision')) {
                const mensaje = objErroresValidacion[clave][0];
                    
                const partes = clave.split('.');
                const index = partes[1];
                    
                erroreslstGuiaRemision += `->${mensaje}<br>`;
            } else {
                const pError = document.querySelector(`.${clave}_error`);
                if (pError) {
                    pError.textContent = objErroresValidacion[clave][0];
                }
            }
        }

        if (erroreslstGuiaRemision) {
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
            toastr.error(erroreslstGuiaRemision, 'ERROR DE VALIDACIÓN');
        }
    }


    function cambiarAlmacenOrigen(){
        dtProductos.ajax.reload();
        lstGuiaRemision.length    =   0;
        limpiarFormSelectProducto();
        limpiarTabla('table_registro_salida_to_guia_detalle');
        destruirDataTable(dtGuiaDetalle);
        pintarTableGuiaDetalle(lstGuiaRemision)
        iniciarDataTableGuiaDetalle();
        $('#almacen_destino').val(null).trigger('change');
        toastr.clear();
        toastr.info('ALMACÉN ORIGEN CAMBIADO');
    }

   

    function cambiarAlmacenDestino(almacen_destino_id){
        toastr.clear();
        const almacen_origen_id =   document.querySelector('#almacen_origen').value;
        if(!almacen_origen_id){
            toastr.error('DEBE SELECCIONAR UN ALMACÉN DE ORIGEN PREVIAMENTE!!!');
            return;
        }
        if(almacen_destino_id == almacen_origen_id){
            $('#almacen_destino').val(null).trigger('change');
            document.querySelector('#almacen_destino').focus();
            toastr.error('EL ALMACÉN DE DESTINO DEBE SER DIFERENTE AL DE ORIGEN!!!');
            return;
        }

    }
    
    function cargarDetalleGuia(){
        const lstDetalleGuia    =   @json($registro_salida_detalle);
        lstDetalleGuia.forEach((item)=>{
            const producto  =   {
                                    producto_nombre:item.producto_nombre,
                                    categoria_nombre:item.categoria_nombre,
                                    marca_nombre:item.marca_nombre,
                                    producto_unidad_medida:item.unidad_medida_nombre,
                                    cantidad:item.cantidad
                                }

            lstGuiaRemision.push(producto);
        })

        limpiarTabla('table_registro_salida_to_guia_detalle');
        destruirDataTable(dtGuiaDetalle);
        pintarTableGuiaDetalle(lstGuiaRemision)
        iniciarDataTableGuiaDetalle();
    }

   
</script>


