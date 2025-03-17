<div class="row mb-3">
    <div class="col-md-6">
        <label for="producto">Producto</label>
        <select id="producto" class="form-select select2_form">
            <option value="">Seleccionar Producto</option>
            <!-- Las opciones de productos se llenarán dinámicamente con Select2 -->
        </select>
    </div>
    <div class="col-md-3">
        <label for="cantidad">Cantidad</label>
        <input type="number" id="cantidad" class="form-control" placeholder="Cantidad">
    </div>
    <div class="col-md-3">
        <label for="unidad_medida">Unidad de Medida</label>
        <input type="text" id="unidad_medida" class="form-control" placeholder="Unidad de Medida" readonly>
    </div>
</div>

<!-- Botón de agregar producto con ícono -->
<button type="button" class="btn btn-success mb-3" onclick="agregarProductoComposicion(platoId)">
    <i class="fa-solid fa-plus"></i> Agregar Producto
</button>

<script>

function agregarProductoComposicion(platoId) {
    const productoId = $('#producto').val();
    const cantidad = $('#cantidad').val();
    const unidadMedida = $('#unidad_medida').val();

    // Validar si los campos son válidos
    if (!productoId || !cantidad || !unidadMedida) {
        toastr.error("Todos los campos deben ser completados.");
        return;
    }

    $.ajax({
        url: '/platos/composicion/guardar',  // Asegúrate de tener la ruta correcta
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}',  // Incluir el token CSRF
            'Accept': 'application/json'  // Asegurarnos de que la respuesta sea en JSON
        },
        data: {
            producto_id: productoId,
            cantidad: cantidad,
            unidad_medida: unidadMedida,
            plato_id: platoId  // Aquí pasamos el plato_id
        },
        success: function(response) {
            if (response.success) {
                // Mostrar un mensaje de éxito usando toastr
                toastr.success("Producto agregado correctamente.");

                // Recargar la tabla de detalles para mostrar la nueva composición
                $('#table_detalle_platos').DataTable().ajax.reload();

                // Eliminar el producto seleccionado del select
                $('#producto option:selected').remove();

                // Limpiar los campos del formulario
                $('#cantidad').val('');
                $('#unidad_medida').val('');
            } else {
                // Mostrar un mensaje de error usando toastr
                toastr.error("Error al agregar el producto.");
            }
        },
        error: function() {
            // Mostrar un mensaje de error usando toastr
            toastr.error("Ocurrió un error al agregar el producto.");
        }
    });
}


    
</script>


