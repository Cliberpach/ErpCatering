<div class="modal fade" id="mdlImportProducto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Importar Producto</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
            <div class="row">
                <div class="col-12 mb-3">
                    <button class="btn btn-danger" onclick="descargarFormatoExcel();"><i class="fa-solid fa-download"></i> Descargar Formato</button>
                </div>
                <div class="col-12">
                    <form action="" method="post" id="formImportarProductos">
                        <label for="formFileSm" style="font-weight: bold;" class="form-label required_field">Subir excel</label>
                        <input id="inputImportExcelProductos" class="form-control form-control-sm" id="formFileSm" type="file" accept=".xlsx, .xls">
                    </form>
                    <span class="productos_import_excel_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-12 mt-3">
                    <div class="table-responsive">
                        @include('registros.productos.tables.table_import_producto')
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
                <div class="col-12 d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" style="margin-right:5px;" data-bs-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary btnRegistrarProducto" type="submit" form="formImportarProductos">
                        <i class="fa-solid fa-upload"></i> Importar
                    </button>
                </div>
                <div class="col-12 d-flex justify-content-start">
                    <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
                </div>   
        </div>
      </div>
    </div>
</div>

<script>
    let dtImportProductos =   null;

    function eventsMdlImportarProductos(){
        document.querySelector('#formImportarProductos').addEventListener('submit',(e)=>{
            e.preventDefault();
            importarProductosExcel();
        })
    }

    function openMdlImportProducto(){
        $('#mdlImportProducto').modal('show');
    }

    function iniciarDataTableImportProductos(){
        dtImportProductos  =   new DataTable('#table_import_producto',{
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


    function descargarFormatoExcel(){
        const ruta  =   @json(route('registros.producto.descargarFormatoExcel'));
        console.log(ruta);
        window.location.href = ruta;
    }

    function importarProductosExcel(){

        const inputImportExcelProductos    =   document.querySelector('#inputImportExcelProductos');

        if(inputImportExcelProductos.files.length === 0){
            toastr.error('DEBE CARGAR UN EXCEL PARA PROCEDER CON LA IMPORTACIÓN');
            return;
        }

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA IMPORTAR EL LISTADO DE PRODUCTOS?",
        text: "Esta operación producirá cambios en el listado de productos!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, IMPORTAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formImportarProductos    =   document.querySelector('#formImportarProductos');
            const formData                  =   new FormData(formImportarProductos);
            const urlImportarProductos     =   @json(route('registros.producto.importarProductosExcel'));

            formData.append('productos_import_excel',inputImportExcelProductos.files[0]);

            Swal.fire({
                title: 'Cargando...',
                html: 'Importando productos ...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlImportarProductos, {
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
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    if('resultado' in res){
                        console.log(res);
                        destruirDataTable(dtImportProductos);
                        limpiarTabla('table_import_producto');
                        pintarTableImportProductos(res.resultado.listadoProductos);
                        iniciarDataTableImportProductos();
                    }
                    dtProductos.ajax.reload(null, false);
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    if('resultado' in res){
                        console.log(res);
                        destruirDataTable(dtImportProductos);
                        limpiarTabla('table_import_producto');
                        pintarTableImportProductos(res.resultado.listadoProductos);
                        iniciarDataTableImportProductos();
                    }
                }

              
            } catch (error) {
                console.log(error)
                toastr.error(error,'ERROR EN LA PETICIÓN IMPORTAR EXCEL');
            }finally{
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

    function pintarTableImportProductos(lstProductos){
        const tbody =   document.querySelector('#table_import_producto tbody');
        let filas   =   ``;
        lstProductos.forEach((lc)=>{
            filas   +=  `<tr>
                            <th>${lc.fila}</th>
                            <td>${lc.error}</td>
                            <td>${lc.nombre}</td>
                            <td>${lc.codigo_barras}</td>
                            <td>${lc.codigo_interno}</td>
                            <td>${lc.categoria}</td>
                            <td>${lc.marca}</td>
                            <td>${lc.unidad_medida}</td>
                            <td>${lc.precio}</td>
                            <td>${lc.stock_minimo}</td>
                        </tr>`;
        })

        tbody.innerHTML =   filas;
    }
</script>