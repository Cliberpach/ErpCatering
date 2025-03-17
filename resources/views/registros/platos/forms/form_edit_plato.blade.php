<form action="" id="formEditarPlato" method="post">
    @csrf
    <div class="modal-body">
        <!-- Primera fila -->
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editarNombre" class="form-label">Nombre del Plato <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="editarNombre" class="form-control" required>
                    <span class="nombre_error msgError" style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editarCalorias" class="form-label">Calorías <span class="text-danger">*</span></label>
                    <input type="text" name="calorias" id="editarCalorias" class="form-control" required>
                    <span class="calorias_error msgError" style="color:red;"></span>
                </div>
            </div>
        </div>

        <!-- Segunda fila -->
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editarProteinas" class="form-label">Proteínas <span class="text-danger">*</span></label>
                    <input type="text" name="proteinas" id="editarProteinas" class="form-control" required>
                    <span class="proteinas_error msgError" style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editarCarbohidratos" class="form-label">Carbohidratos <span class="text-danger">*</span></label>
                    <input type="text" name="carbohidratos" id="editarCarbohidratos" class="form-control" required>
                    <span class="carbohidratos_error msgError" style="color:red;"></span>
                </div>
            </div>
        </div>

        <!-- Tercera fila -->
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editarGrasas" class="form-label">Grasas <span class="text-danger">*</span></label>
                    <input type="text" name="grasas" id="editarGrasas" class="form-control" required>
                    <span class="grasas_error msgError" style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editarPeso" class="form-label">Peso <span class="text-danger">*</span></label>
                    <input type="text" name="peso" id="editarPeso" class="form-control" required>
                    <span class="peso_error msgError" style="color:red;"></span>
                </div>
            </div>
        </div>

        <!-- Cuarta fila -->
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editarCosto" class="form-label">Costo <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="costo" id="editarCosto" class="form-control" required>
                    <span class="costo_error msgError" style="color:red;"></span>
                </div>
            </div>
        </div>

        
    </div>
</form>
