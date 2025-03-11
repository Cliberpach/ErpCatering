<!-- Modal para ingresar la contraseña del administrador -->
<div class="modal fade" id="modalPassword" tabindex="-1" aria-labelledby="modalPasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPasswordLabel">Acceso Requerido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="password">Ingrese la contraseña del administrador:</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" autocomplete="new-password" required />
                <div class="invalid-feedback" id="passwordError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="verifyPasswordBtn">Verificar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para asignar horas extra -->
<div class="modal fade" id="modalHorasExtra" tabindex="-1" aria-labelledby="modalHorasExtraLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHorasExtraLabel">Asignar Horas Extra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formHorasExtra">
                    <input type="hidden" id="horasExtraId" name="asistencia_detalle_id">
                    <div class="mb-3">
                        <label for="horasExtra" class="form-label">Horas Extra</label>
                        <input type="text" class="form-control" id="horasExtra" name="horas_extra" placeholder="Horas extra" required>
                        <div class="invalid-feedback" id="horasExtraError"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
