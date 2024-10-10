@extends('layouts.layout')
@section('title-page')
    REGISTRAR REQUERIMIENTO
@endsection

@section('requerimientos-collapsed', '')
@section('requerimientos-expanded', 'true')
@section('requerimientos-show', 'show')
@section('requerimientos-active', 'active')

@section('section-page')

@include('requerimientos.requerimientos.modals.modal_productos')
@include('requerimientos.requerimientos.modals.modal_edit_item')
@include('reutilizables.modals.proveedores.mdl_create_proveedor')


<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos del Requerimiento <i class="fa-solid fa-bell-concierge"></i></h6>
    </div>
    <div class="card-body">
        @include('requerimientos.requerimientos.forms.form_create_requerimiento')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formRegistrarRequerimiento">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>
    let dtProductos                 =   null;
    let dtRequerimientoDetalle      =   null;
    const lstRequerimientos         =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        iniciarDataTableProductos();
        iniciarDataTableRequerimientoDetalle();
        events();
    })

    function events(){
        eventsMdlEditItem();
        eventsMdlCreateProveedor();

        document.querySelector('#formRegistrarRequerimiento').addEventListener('submit',(e)=>{
            e.preventDefault();
            const validacion    =   validacionRegistrarRequerimiento();
            if(validacion){
                registrarRequerimiento();
            }
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('requerimientos.requerimientos.index')}}';
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

    function iniciarDataTableProductos(){
        const urlGetProductos   =   @json(route('requerimientos.requerimientos.getProductos'));
        
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
        dtRequerimientoDetalle  =   new DataTable('#table_requerimiento_detalle',{
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

    function validacionRegistrarRequerimiento(){
        if(lstRequerimientos.length === 0){
            toastr.error('EL DETALLE DEL REQUERIMIENTO ESTÁ VACÍO!!!');
            return false;
        }
        return true;
    }

    function agregarProducto(producto,cantidad){
        producto.cantidad   =   cantidad;

        const indiceProducto    =   lstRequerimientos.findIndex((p)=>{
            return p.producto_id == producto.producto_id;
        })

        if(indiceProducto !== -1){
            toastr.error('EL PRODUCTO YA EXISTE EN EL DETALLE');
            return;
        }

        lstRequerimientos.push(producto);
        limpiarTabla('table_requerimiento_detalle');
        destruirDataTableRequerimientoDetalle();
        pintarTableRequerimientoDetalle(lstRequerimientos);
        iniciarDataTableRequerimientoDetalle();
        toastr.info('PRODUCTO AGREGADO AL DETALLE');
    }

    function pintarTableRequerimientoDetalle(lstItems){
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

        const tbody =   document.querySelector('#table_requerimiento_detalle tbody');
        tbody.innerHTML =   filas;
    }

    
    function destruirDataTableProductos(){
        if(dtProductos){
            dtProductos.destroy();
            dtProductos =   null;
        }
    }

    function destruirDataTableRequerimientoDetalle(){
        if(dtRequerimientoDetalle){
            dtRequerimientoDetalle.destroy();
            dtRequerimientoDetalle =   null;
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


    function registrarRequerimiento(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR EL REQUERIMIENTO?",
        text: "Nuevo Requerimiento!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            const token                             =   document.querySelector('input[name="_token"]').value;
            const formRegistrarRequerimiento        =   document.querySelector('#formRegistrarRequerimiento');
            
            const formData                          =   new FormData();
            formData.append('supervisor_id',@json($colaborador->id));
            formData.append('proyecto_id',@json($proyecto->id));
            formData.append('fecha_atencion',document.querySelector('#fecha_atencion').value);
            formData.append('proveedor',document.querySelector('#proveedor').value);

            const urlRegistrarRequerimiento         =   @json(route('requerimientos.requerimientos.store'));

            formData.append('lstRequerimientos',JSON.stringify(lstRequerimientos))

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nuevo requerimiento...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarRequerimiento, {
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
                    const requerimiento_index       =   @json(route('requerimientos.requerimientos.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href            =   requerimiento_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR REQUERIMIENTO');
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


