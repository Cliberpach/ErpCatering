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

    function cargarProductos(platoId) {
    // Limpiar el select cuando se cambia de modal
    const select = $('#producto');
    select.empty();  // Limpiar las opciones anteriores
    select.append('<option value="">Seleccionar Producto</option>');  // Opción inicial

    $.ajax({
        url: '/platos/composicion/productos',  // URL para obtener los productos
        method: 'GET',
        data: { plato_id: platoId },  // Enviar el plato_id al backend para filtrar productos
        success: function(response) {
            // Si no hay productos disponibles
            if (response.products.length === 0) {
                select.append('<option value="" disabled>No hay productos disponibles para este plato</option>');
                return;
            }

            // Llenar el select con los productos obtenidos
            response.products.forEach(function(product) {
                select.append(`<option value="${product.id}" data-unidad="${product.unidad_medida}">${product.nombre}</option>`);
            });
        },
        error: function() {
            // Manejo de error (aquí podrías agregar otro mensaje de error si es necesario)
            console.log('Error al cargar los productos');
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

        cargarProductos(platoId);

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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarProductoComposicion(${data.id}, ${platoId})">
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

function eliminarProductoComposicion(detalleId, platoId) {
    // Mostrar la alerta de confirmación con Swal
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Si el usuario confirma, proceder con la eliminación
            $.ajax({
                url: '/platos/composicion/eliminar',  // URL de eliminación
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}',
                    'Accept': 'application/json',
                },
                data: {
                    detalle_id: detalleId,  // ID del detalle de la composición a eliminar
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);

                        // Recargar la tabla
                        $('#table_detalle_platos').DataTable().ajax.reload();

                        // Volver a cargar los productos disponibles en el select
                        cargarProductos(platoId);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error("Ocurrió un error al eliminar el producto.");
                }
            });
        } else {
            // Si el usuario cancela, mostrar un mensaje de cancelación
            Swal.fire(
                'Cancelado',
                'El producto no fue eliminado.',
                'info'
            );
        }
    });
}


</script>
