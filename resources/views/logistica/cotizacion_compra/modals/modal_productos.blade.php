<div class="modal fade" id="mdlProductos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Seleccionar Producto</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <div class="row">
               <div class="col-6">
                    <label for="categoria" style="font-weight: bold;">CATEGORÍA</label>

                    <select data-placeholder="Seleccione una opción" name="categoria" id="categoria" class="select2_form" onchange="getProductosByCategoria(this.value)">
                        <option></option>
                        @foreach ($categorias as $categoria)
                            <option value="{{$categoria->id}}">{{$categoria->descripcion}}</option>
                        @endforeach
                    </select>
               </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        @include('logistica.cotizacion_compra.tables.table_productos')
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
           
        </div>
      </div>
    </div>
</div>

<script>
    const lstTableProductos =   [];
    const producto_elegido  =   {
                                    producto_id:null,
                                    producto_nombre:null,
                                    categoria_nombre:null,
                                    marca_nombre:null,
                                    producto_unidad_medida:null,
                                    cantidad:null
                                }

    function eventsMdlProductos(){

    }

    function openMdlProductos(){
        $('#mdlProductos').modal('show');
    }

    function seleccionarProducto(producto_id) {

        const indexProducto = lstTableProductos.findIndex((p)=>{
            return p.producto_id == producto_id;
        })

        if(indexProducto === -1){
            toatr.error('ERROR AL SELECCIONAR PRODUCTO');
            return;
        }

        //======= SETTEAR PRODUCTO =======
        const producto                              =   lstTableProductos[indexProducto];
        document.querySelector('#producto').value   =   producto.producto_nombre;
        document.querySelector('#unidad').value     =   producto.producto_unidad_medida;

        producto_elegido.producto_id            =   producto.producto_id;
        producto_elegido.producto_nombre        =   producto.producto_nombre;
        producto_elegido.categoria_nombre       =   producto.categoria_nombre;
        producto_elegido.marca_nombre           =   producto.marca_nombre;
        producto_elegido.producto_unidad_medida =   producto.producto_unidad_medida;

        console.log('PRODUCTO ELEGIDO');
        console.log(producto_elegido);


        $('#mdlProductos').modal('hide');
        document.querySelector('#cantidad').focus();

    }

</script>