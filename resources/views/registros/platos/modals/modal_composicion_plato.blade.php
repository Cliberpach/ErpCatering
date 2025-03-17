<!-- Modal Ver y Agregar Composición del Plato -->
<div class="modal fade" id="mdlComposicionPlato" tabindex="-1" aria-labelledby="modalComposicionPlatoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalComposicionPlatoLabel">Composición del Plato</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Formulario para agregar producto a la composición -->
                @include('registros.platos.forms.composicion_agregar')

                

                @include('registros.platos.tables.table_composicion_plato')
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<script>

    let dtDetallePlatos = null;

    function eventsMdlComposicion(){
        //document.querySelector('#formRegistrarPlato').addEventListener('submit',(e)=>{
        //    e.preventDefault();
        //    registrarComposicion();

        //})

        $('#mdlComposicionPlato').on('hidden.bs.modal', function (e) {
            //const   formRegistrarPlato    =   document.querySelector('#formRegistrarPlato');
            //formRegistrarPlato.reset();

            $('#producto').val('').trigger('change');
            $('#cantidad').val(''); 
            $('#unidad_medida').val(''); 
            
            
            limpiarErroresValidacion('msgError');
        });

        
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            allowClear: true,
            dropdownParent: $('#mdlComposicionPlato')
        } );
    

    }

    function cargarProductos() {
    $.ajax({
        url: '/platos/composicion/productos', 
        method: 'GET',
        success: function(response) {
            const select = $('#producto');
            select.empty(); 
            select.append('<option value="">Seleccionar Producto</option>');

    
            response.products.forEach(function(product) {
                select.append(`<option value="${product.id}" data-unidad="${product.unidad_medida}">${product.nombre}</option>`);
            });
        },
        error: function() {
            alert('Error al cargar los productos');
        }
    });
}

// Función para actualizar el campo de "Unidad de Medida" cuando se selecciona un producto
$('#producto').on('change', function() {
    const selectedOption = $(this).find('option:selected'); 
    const unidadMedida = selectedOption.data('unidad'); 

    if (unidadMedida) {
        $('#unidad_medida').val(unidadMedida); 
    } else {
        $('#unidad_medida').val('');
    }
});



    function openMdlComposicion(platoId) {
        $('#table_detalle_platos tbody').empty();
        window.platoId = platoId;

        iniciarDataTableDetallePlatos(platoId);

        cargarProductos();

        $('#mdlComposicionPlato').modal('show');
    }



    function pintarErroresValidacion(objErroresValidacion){
        for (let clave in objErroresValidacion) {
            const pError        =   document.querySelector(`.${clave}_error`);
            pError.textContent  =   objErroresValidacion[clave][0];
        }
    }

    function limpiarErroresValidacion() {
        document.querySelectorAll('.msgError').forEach((el) => {
            el.textContent = '';
        });
    }

    function iniciarDataTableDetallePlatos(platoId) {
    const urlGetDetallePlatos = `/platos/getDetallePlatos/${platoId}`; // URL para obtener los detalles de la composición

    if ($.fn.dataTable.isDataTable('#table_detalle_platos')) {
        $('#table_detalle_platos').DataTable().clear().destroy();
    }

    dtDetallePlatos = new DataTable('#table_detalle_platos', {
        serverSide: true,
        processing: true,
        pageLength: 50,

        ajax: {
            url: urlGetDetallePlatos,
            type: 'GET',
        },
        columns: [
            { data: 'producto', name: 'producto' },
            { data: 'cantidad', name: 'cantidad' },
            { data: 'unidad_medida', name: 'unidad_medida' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group dropstart">
                            <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 150px; overflow-y: auto;">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    `;
                },
                name: 'actions',
                orderable: false,
                searchable: false
            }
        ],
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


</script>
