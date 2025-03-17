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
        url: '/platos/composicion/guardar',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}', 
            'Accept': 'application/json' 
        },
        data: {
            producto_id: productoId,
            cantidad: cantidad,
            unidad_medida: unidadMedida,
            plato_id: platoId 
        },
        success: function(response) {
            if (response.success) {
               
                toastr.success("Producto agregado correctamente.");

                $('#table_detalle_platos').DataTable().ajax.reload();

                $('#producto option:selected').remove();

                $('#cantidad').val('');
                $('#unidad_medida').val('');
            } else {
                toastr.error("Error al agregar el producto.");
            }
        },
        error: function() {
            toastr.error("Ocurrió un error al agregar el producto.");
        }
    });
}


    
</script>


