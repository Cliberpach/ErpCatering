@extends('layouts.layout')
@section('title-page')
    REGISTRAR COMPRA
@endsection

@section('section-page')

@include('logistica.registro_compra.modals.modal_productos')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos de la Compra <i class="fa-solid fa-toolbox"></i></h6>
    </div>
    <div class="card-body">
        @include('logistica.registro_compra.forms.form_create_compra')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formRegistrarCompra">
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
    const lstCompraDetalle  =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        iniciarDataTableProductos();
        iniciarDataTableCompraDetalle();
        events();
    })

    function events(){
    
        document.querySelector('#formRegistrarCompra').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarProducto();
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('registros.producto.index')}}';
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
                agregarProducto(producto_elegido,inputCantidad.value);
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

    function agregarProducto(producto,cantidad){
        producto.cantidad   =   cantidad;

        const indiceProducto    =   lstCompraDetalle.findIndex((p)=>{
            return p.producto_id == producto.producto_id;
        })

        if(indiceProducto !== -1){
            toastr.error('EL PRODUCTO YA EXISTE EN EL DETALLE');
            return;
        }

        lstCompraDetalle.push(producto);
        limpiarTabla('table_compra_detalle');
        destruirDataTableCompraDetalle();
        pintarTableCompraDetalle(lstCompraDetalle);
        iniciarDataTableCompraDetalle();
        toastr.info('PRODUCTO AGREGADO AL DETALLE');
    }

    function pintarTableCompraDetalle(lstItems){
        let filas   =   ``;
        lstItems.forEach((producto)=>{
            filas   +=  `<tr>
                            <th></th>
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
            const urlGetProductosByCategoria    =   @json(route('logistica.registro_compra.getProductosByCategoria', ['categoria_id' => 'CATEGORIA_ID']));
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


    function registrarProducto(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR EL PRODUCTO?",
        text: "Se creará un nuevo producto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formRegistrarCompra       =   document.querySelector('#formRegistrarCompra');
            const formData                  =   new FormData(formRegistrarCompra);
            const urlRegistrarProducto      =   @json(route('registros.producto.store'));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nuevo producto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarProducto, {
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
                    const producto_index     =   @json(route('registros.producto.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   producto_index;
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

</script>


