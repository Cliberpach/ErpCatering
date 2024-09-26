<div class="modal fade" id="mdlProductos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Seleccionar Producto</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <label for="categoria" style="font-weight: bold;">CATEGORÍA</label>

                    <select data-placeholder="Seleccione una opción" name="categoria" id="categoria" class="select2_form" onchange="dtProductos.ajax.reload();">
                        <option></option>
                        @foreach ($categorias as $categoria)
                            <option value="{{$categoria->id}}">{{$categoria->descripcion}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <label for="marca" style="font-weight: bold;">MARCA</label>

                    <select data-placeholder="Seleccione una opción" name="marca" id="marca" class="select2_form" onchange="dtProductos.ajax.reload();">
                        <option></option>
                        @foreach ($marcas as $marca)
                            <option value="{{$marca->id}}">{{$marca->descripcion}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        @include('compras.registro_compra.tables.table_productos')
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
    const producto_elegido  =   {
                                    producto_id:null,
                                    producto_nombre:null,
                                    categoria_nombre:null,
                                    marca_nombre:null,
                                    producto_unidad_medida:null,
                                    cantidad:null,
                                    precio:null,
                                    almacen_id:null
                                }

    function eventsMdlProductos(){

    }

    function openMdlProductos(){
        $('#mdlProductos').modal('show');
    }

    function seleccionarProducto(producto_id) {

        const fila  =   getRowById(dtProductos,producto_id);
        
        if(!fila){
            toastr.error('NO SE ENCONTRÓ EL PRODUCTO EN LA TABLA PRODUCTOS');
            return;
        }

        console.log(fila);

        //======= SETTEAR PRODUCTO =======
        const producto                              =   fila;
        document.querySelector('#producto').value   =   producto.nombre;
        document.querySelector('#unidad').value     =   producto.unidad_medida_nombre;
        document.querySelector('#precio').value     =   producto.precio;
            


        producto_elegido.producto_id            =   producto.id;
        producto_elegido.producto_nombre        =   producto.nombre;
        producto_elegido.categoria_nombre       =   producto.categoria_nombre;
        producto_elegido.marca_nombre           =   producto.marca_nombre;
        producto_elegido.producto_unidad_medida =   producto.unidad_medida_nombre;
        producto_elegido.precio                 =   producto.precio;

        console.log('PRODUCTO ELEGIDO');
        console.log(producto_elegido);


        $('#mdlProductos').modal('hide');
        document.querySelector('#cantidad').focus();

    }

    function limpiarFormSelectProducto(){
        const inputProducto =   document.querySelector('#producto');
        const inputUnidad   =   document.querySelector('#unidad');
        const inputCantidad =   document.querySelector('#cantidad');
        const inputPrecio   =   document.querySelector('#precio');

        inputProducto.value =   '';
        inputUnidad.value   =   '';
        inputCantidad.value =   '';
        inputPrecio.value   =   '';
        producto_elegido.producto_id            =   null;
        producto_elegido.producto_nombre        =   null;
        producto_elegido.categoria_nombre       =   null;
        producto_elegido.marca_nombre           =   null;
        producto_elegido.producto_unidad_medida =   null;
        producto_elegido.cantidad               =   null;
        producto_elegido.precio                 =   null;
        $('#almacen').val(1).trigger('change');


    }

</script>