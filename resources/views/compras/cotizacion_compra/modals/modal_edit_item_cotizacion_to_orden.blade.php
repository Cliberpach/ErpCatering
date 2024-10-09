<div class="modal fade" id="mdlEditItemCotizacionToOrden" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Item</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

           @include('compras.cotizacion_compra.forms.form_edit_item_cotizacion_to_orden')

        </div>
        <div class="modal-footer">
            
            <div class="col-12 d-flex justify-content-end">
                <button type="button" style="margin-right:5px;" class="btn btn-secondary mr-1" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formEditItemCotizacionToOrden" class="btn btn-primary" >Guardar</button>
            </div>
            <div class="col-12 d-flex" style="padding-left:5px;">
                <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
            </div>
        </div>
      </div>
    </div>
</div>

<script>

    const producto_edicion   =   {producto_id:null};

    function eventsMdlEditItem(){
        document.addEventListener('click',(e)=>{

            if(e.target.classList.contains('btnEditItem')){
                toastr.clear();

                const producto_id   =   e.target.getAttribute('data-producto-id');

                if(!producto_id){
                    toastr.error('ERROR AL SETTEAR PRODUCTO');
                    return;
                }

                setProducto(producto_id);
                openMdlEditItem();
            }

            if(e.target.classList.contains('btnDeleteItem')){
                toastr.clear();

                const producto_id       =   e.target.getAttribute('data-producto-id');

                const res_delete_item   =    deleteItem(producto_id);

                if(res_delete_item){
                    limpiarTabla('table_cotizacion_to_orden_detalle');
                    destruirDataTableCompraDetalle();
                    pintarTableCompraDetalle(lstCotizacionCompra);
                    iniciarDataTableCotizacionToOrdenDetalle();
                    const estado    =   document.querySelector('#igv').checked;
                    const valorIgv  =   document.querySelector('#igv').value;
                    const montos    =   calcularMontos(lstCotizacionCompra,estado,valorIgv);
                    const moneda    =   document.querySelector('#moneda').value;

                    pintarTableMontos(montos,moneda);
                    toastr.success('ITEM ELIMINADO!!');
                }    
            }

        })

        document.querySelector('#formEditItemCotizacionToOrden').addEventListener('submit',(e)=>{

            mostrarAnimacion1();
            e.preventDefault();
            const dataFormEdit          =   getDataFormEdit();
            const validacionFormEdit    =   validarDataFormEdit(dataFormEdit);

            if(validacionFormEdit){
                const actualizacion = actualizarItem(dataFormEdit);
                if(actualizacion){
                    limpiarTabla('table_cotizacion_to_orden_detalle');
                    destruirDataTableCompraDetalle();
                    pintarTableCompraDetalle(lstCotizacionCompra);
                    iniciarDataTableCotizacionToOrdenDetalle();

                    const estado    =   document.querySelector('#igv').checked;
                    const valorIgv  =   document.querySelector('#igv').value;
                    const montos    =   calcularMontos(lstCotizacionCompra,estado,valorIgv);
                    const moneda    =   document.querySelector('#moneda').value;

                    pintarTableMontos(montos,moneda);

                    $('#mdlEditItemCotizacionToOrden').modal('hide');
                    toastr.success('ITEM ACTUALIZADO');
                }
            }
            ocultarAnimacion1();

        })
    }

    function openMdlEditItem(){
        $('#mdlEditItemCotizacionToOrden').modal('show');
    }

    function deleteItem(producto_id){

        const indiceProducto    =   lstCotizacionCompra.findIndex((lcd)=>{
            return lcd.producto_id == producto_id;
        })

        if(indiceProducto === -1){
            toastr.error('NO SE ENCONTRÓ EL ITEM EN EL DETALLE!!!');
            return false;
        }

        lstCotizacionCompra.splice(indiceProducto,1);
        return true;

    }

    function actualizarItem(dataFormEdit){

        //======= GRABANDO =========
        const indiceProducto    =   lstCotizacionCompra.findIndex((lcd)=>{
            return lcd.producto_id == producto_edicion.producto_id;
        })

        if(indiceProducto === -1){
            toastr.error('NO SE ENCONTRÓ EL PRODUCTO A EDITAR');
            return false;
        }

        lstCotizacionCompra[indiceProducto].cantidad      =   dataFormEdit.cantidad;
        lstCotizacionCompra[indiceProducto].precio        =   dataFormEdit.precio;
        lstCotizacionCompra[indiceProducto].total         =   parseFloat(dataFormEdit.precio) * parseFloat(dataFormEdit.cantidad);
        lstCotizacionCompra[indiceProducto].almacen_id    =   dataFormEdit.almacen_id;
        lstCotizacionCompra[indiceProducto].almacen_nombre=   dataFormEdit.almacen_nombre;

        return true;
    }

    function getDataFormEdit(){
        const cantidad      =   document.querySelector('#item_cantidad_edit').value;
        const precio        =   document.querySelector('#item_precio_edit').value;
        const almacen_id    =   $('#item_almacen_edit').val();
        const almacen_nombre= $('#item_almacen_edit option:selected').text();


        const data      =   {cantidad,precio,almacen_id,almacen_nombre};
        return data;
    }

    function validarDataFormEdit(data){
        let validacion  =   false;

        if(data.cantidad === null){
            document.querySelector('#item_cantidad_edit').focus();
            toastr.error('DEBE INGRESAR UNA CANTIDAD!!');
            return validacion;
        }

        if(data.cantidad == 0){
            document.querySelector('#item_cantidad_edit').focus();
            toastr.error('LA CANTIDAD DEBE SER MAYOR A 0!!');
            return validacion;
        }

        if(data.precio === null){
            document.querySelector('#item_precio_edit').focus();
            toastr.error('DEBE INGRESAR UN PRECIO!!');
            return validacion;
        }

        if(data.precio == 0){
            document.querySelector('#item_precio_edit').focus();
            toastr.error('EL PRECIO DEBE SER MAYOR A 0!!');
            return validacion;
        }

        if(data.almacen_id === null){
            document.querySelector('#item_almacen_edit').focus();
            toastr.error('DEBE SELECCIONAR UN ALMACÉN!!');
            return validacion;
        }

        return true;
    }

    function setProducto(producto_id){

        const productoIndice    =   lstCotizacionCompra.findIndex((lcd)=>{
            return lcd.producto_id == producto_id;
        })

        if(productoIndice === -1){
            toastr.error('NO SE ENCUENTRA EL PRODUCTO EN EL DETALLE!!');
            return;
        }

        const producto_find   =   lstCotizacionCompra[productoIndice];

        document.querySelector('#item_nombre_edit').value   =   producto_find.producto_nombre;   
        document.querySelector('#item_unidad_edit').value   =   producto_find.producto_unidad_medida;   
        document.querySelector('#item_cantidad_edit').value =   producto_find.cantidad;   
        document.querySelector('#item_precio_edit').value   =   producto_find.precio;
        $('#item_almacen_edit').val(producto_find.almacen_id).trigger('change');
        producto_edicion.producto_id    =   producto_find.producto_id;

    }
</script>