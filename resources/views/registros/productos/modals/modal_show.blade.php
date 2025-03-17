<div class="modal fade" id="mdlShowProducto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Encabezado de la tarjeta -->
            <div class="modal-header text-light">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ver Producto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Cuerpo de la tarjeta -->
            <div class="modal-body">
                <div class="row">
                    <!-- Sección de Información del Producto -->
                    <div class="col-12">
                        <div class="border rounded p-4 bg-white shadow-sm">
                            <h5 class="text-primary border-bottom pb-2 mb-3">Detalles del Producto</h5>
                            
                            <!-- Mostrar los datos -->
                            <div class="row mb-3">
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">Categoría:</strong>
                                    <span class="text-muted" id="spanCategoria"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">Marca:</strong>
                                    <span class="text-muted" id="spanMarca"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                                    <strong class="text-primary">Tipo de Producto:</strong>
                                    <span class="text-muted" id="spanTipo"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">Producto:</strong>
                                    <span class="text-muted" id="spanProducto"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">Precio:</strong>
                                    <span class="text-muted" id="spanPrecio"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">Unidad de Medida:</strong>
                                    <span class="text-muted" id="spanUnidad"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <strong class="text-primary">CÓDIGO INTERNO:</strong>
                                    <span class="text-muted" id="spanCodigoInterno"></span>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">                                    
                                    <img class="img-fluid" src="#" alt="CÓDIGO BARRAS" id="imgCodigoBarras">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Stocks (se puede agregar después) -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="border rounded p-4 bg-white shadow-sm">
                            <h6 class="text-primary mb-3 border-bottom pb-2">Stock en Almacenes</h6>
                            <div class="table-responsive">
                                @include('registros.productos.tables.table_stocks')
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie de la tarjeta -->
            <div class="modal-footer text-white d-flex justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>

    let dtStocks    =   null;

    async function openMdlShowProducto(id){
        await getShowProducto(id);
        $('#mdlShowProducto').modal('show');
    }

    $('#mdlShowProducto').on('hidden.bs.modal', function (e) {
        limpiarModalShow();
    });


    async function getShowProducto(producto_id){
        toastr.clear();
        const token                 =   document.querySelector('input[name="_token"]').value;
        const urlShowProducto       =   `{{ route('registros.producto.show', ':id') }}`.replace(':id', producto_id);

        try {
            mostrarAnimacion1();
            const response  =   await fetch(urlShowProducto, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                    });

            const   res =   await response.json();
                                
            if(res.success){
                pintarProducto(res.producto);
                limpiarTabla('table_stocks');
                destruirDataTable(dtStocks);
                pintarTableStocks(res.stocks);
                iniciarDataTableStocks();
                toastr.success(res.message,'OPERACIÓN COMPLETADA');
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR');
            }

              
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VER PRODUCTO');
        }finally{
            ocultarAnimacion1();
        }
          
    }

    function iniciarDataTableStocks(){
        dtStocks  =   new DataTable('#table_stocks',{
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

    function pintarProducto(producto){
        document.querySelector('#spanCategoria').textContent        =   producto.categoria_nombre;
        document.querySelector('#spanMarca').textContent            =   producto.marca_nombre;
        document.querySelector('#spanProducto').textContent         =   producto.producto_nombre;
        document.querySelector('#spanTipo').textContent             =   producto.tipo_producto;
        document.querySelector('#spanPrecio').textContent           =   producto.producto_precio;
        document.querySelector('#spanUnidad').textContent           =   producto.unidad_medida_nombre;
        document.querySelector('#spanCodigoInterno').textContent    =   producto.codigo_interno;
        document.querySelector('#imgCodigoBarras').src              =   @json(asset(''))+producto.ruta_codigo_barras;

    }

    function pintarTableStocks(stocks){
        let filas   =   ``;
        const tbody =   document.querySelector('#table_stocks tbody');
        stocks.forEach((s)=>{
            filas   +=  `<tr>
                            <th>${s.almacen_nombre}</th>
                            <td>${s.producto_stock}</td>`;
        })
        tbody.innerHTML =   filas;
    }

    function limpiarModalShow(){
        document.querySelector('#spanCategoria').textContent    =   '';
        document.querySelector('#spanMarca').textContent        =   '';
        document.querySelector('#spanProducto').textContent     =   '';
        document.querySelector('#spanPrecio').textContent       =   '';
        document.querySelector('#spanUnidad').textContent       =   '';
    }
</script>