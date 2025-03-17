<form id="formRegistrarPlato">
    <div class="modal-body">
        <div class="row">
            <!-- Nombre del plato -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Plato <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                    <span class="nombre_error msgError" style="color:red;"></span>
                </div>
            </div>

            <!-- Calorías -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="calorias" class="form-label">Calorías <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="calorias" name="calorias" required>
                    <span class="calorias_error msgError" style="color:red;"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Proteínas -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="proteinas" class="form-label">Proteínas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="proteinas" name="proteinas" required>
                    <span class="proteinas_error msgError" style="color:red;"></span>
                </div>
            </div>

            <!-- Carbohidratos -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="carbohidratos" class="form-label">Carbohidratos <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="carbohidratos" name="carbohidratos" required>
                    <span class="carbohidratos_error msgError" style="color:red;"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Grasas -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="grasas" class="form-label">Grasas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="grasas" name="grasas" required>
                    <span class="grasas_error msgError" style="color:red;"></span>
                </div>
            </div>

            <!-- Peso -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="peso" class="form-label">Peso <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="peso" name="peso" required>
                    <span class="peso_error msgError" style="color:red;"></span>
                </div>
            </div>
        </div>

        <div class="row">

            <!-- Costo -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="costo" class="form-label">Costo <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="costo" name="costo" required>
                    <span class="costo_error msgError" style="color:red;"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <small class="text-muted me-auto">
            <span class="text-danger">*</span> Campos obligatorios.
        </small>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa-solid fa-xmark"></i> Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Guardar
        </button>
    </div>
</form>
