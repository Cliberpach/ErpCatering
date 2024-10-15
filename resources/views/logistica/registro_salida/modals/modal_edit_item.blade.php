<div class="modal fade" id="mdlEditItemRegistroSalida" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Item</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

           @include('logistica.registro_salida.forms.form_edit_item')

        </div>
        <div class="modal-footer">
            
            <div class="col-12 d-flex justify-content-end">
                <button type="button" style="margin-right:5px;" class="btn btn-secondary mr-1" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formEditItem" class="btn btn-primary" >Guardar</button>
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

    function eventsMdlEditItemRegistroSalida(){
        document.addEventListener('click',(e)=>{

            if(e.target.classList.contains('btnEditItem')){
                toastr.clear();

                const producto_id   =   e.target.getAttribute('data-producto-id');

                if(!producto_id){
                    toastr.error('ERROR AL SETTEAR PRODUCTO');
                    return;
                }

                producto_edicion.producto_id    =   producto_id;

                setProducto(producto_id);
                openMdlEditItemRegistroSalida();
            }

            if(e.target.classList.contains('btnDeleteItem')){
                toastr.clear();

                const producto_id       =   e.target.getAttribute('data-producto-id');

                const res_delete_item   =    deleteItem(producto_id);

                if(res_delete_item){
                    limpiarTabla('table_salida_detalle');
                    destruirDataTableSalidaDetalle();
                    pintarTableSalidaDetalle(lstSalida);
                    iniciarDataTableSalidaDetalle();
                    toastr.success('ITEM ELIMINADO!!');
                }

                
            }

        })

        document.querySelector('#item_cantidad_edit').addEventListener('input',(e)=>{
            const select_almacen    =   document.querySelector('#almacen_origen');
            const cantidad          =   e.target.value;
            const producto_id       =   producto_edicion.producto_id;

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

            validarCantidad(select_almacen.value,producto_id,cantidad,e.target);
        })

        document.querySelector('#formEditItem').addEventListener('submit',(e)=>{

            mostrarAnimacion1();
            e.preventDefault();
            const dataFormEdit          =   getDataFormEdit();
            const validacionFormEdit    =   validarDataFormEdit(dataFormEdit);

            if(validacionFormEdit){
                const actualizacion = actualizarItem(dataFormEdit);
                if(actualizacion){
                    limpiarTabla('table_salida_detalle');
                    destruirDataTableSalidaDetalle();
                    pintarTableSalidaDetalle(lstSalida);
                    iniciarDataTableSalidaDetalle();
                    $('#mdlEditItemRegistroSalida').modal('hide');
                    toastr.success('ITEM ACTUALIZADO');
                }
            }
            ocultarAnimacion1();

        })
    }

    function openMdlEditItemRegistroSalida(){
        $('#mdlEditItemRegistroSalida').modal('show');
    }

    function deleteItem(producto_id){

        const indiceProducto    =   lstSalida.findIndex((lcd)=>{
            return lcd.producto_id == producto_id;
        })

        if(indiceProducto === -1){
            toastr.error('NO SE ENCONTRÓ EL ITEM EN EL DETALLE!!!');
            return false;
        }

        lstSalida.splice(indiceProducto,1);
        return true;

    }

    function actualizarItem(dataFormEdit){

        //======= GRABANDO =========
        const indiceProducto    =   lstSalida.findIndex((lcd)=>{
            return lcd.producto_id == producto_edicion.producto_id;
        })

        if(indiceProducto === -1){
            toastr.error('NO SE ENCONTRÓ EL PRODUCTO A EDITAR');
            return false;
        }

        lstSalida[indiceProducto].cantidad   =   dataFormEdit.cantidad;
        return true;
    }

    function getDataFormEdit(){
        const cantidad  =   document.querySelector('#item_cantidad_edit').value;
        const data      =   {cantidad};
        return data;
    }

    function validarDataFormEdit(data){
        let validacion  =   false;

        if(data.cantidad === null){
            toastr.error('DEBE INGRESAR UNA CANTIDAD!!');
            return validacion;
        }

        if(data.cantidad == 0){
            toastr.error('LA CANTIDAD DEBE SER MAYOR A 0!!');
            return validacion;
        }

        return true;
    }

    function setProducto(producto_id){

        const productoIndice    =   lstSalida.findIndex((lcd)=>{
            return lcd.producto_id == producto_id;
        })

        if(productoIndice === -1){
            toastr.error('NO SE ENCUENTRA EL PRODUCTO EN EL DETALLE!!');
            return;
        }

        const producto_find   =   lstSalida[productoIndice];

        document.querySelector('#item_nombre_edit').value   =   producto_find.producto_nombre;   
        document.querySelector('#item_unidad_edit').value   =   producto_find.producto_unidad_medida;   
        document.querySelector('#item_cantidad_edit').value =   producto_find.cantidad;   
        producto_edicion.producto_id    =   producto_find.producto_id;

    }
</script>