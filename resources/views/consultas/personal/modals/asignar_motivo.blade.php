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

<!-- Modal para Asignar Motivo de Permiso -->
<div class="modal fade" id="modalAsignarPermiso" tabindex="-1" aria-labelledby="modalAsignarPermisoLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAsignarPermisoLabel">Asignar Motivo de Permiso</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formMotivoPermiso">
            @csrf
            <input type="hidden" id="asistenciaDetalleId" name="asistencia_detalle_id">
            <div class="mb-3">
              <label for="motivoSelect" class="form-label">Motivo de Descanso</label>
              <select id="motivoSelect" name="motivo_id" class="form-select select2" required>
                <!-- Los motivos se cargarán aquí via AJAX -->
              </select>
            </div>
            <div class="mb-3">
              <label for="motivoPermiso" class="form-label">Motivo de Permiso</label>
              <textarea class="form-control" id="motivoPermiso" name="motivo_permiso" rows="3" required></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  

  <script>
    
  </script>