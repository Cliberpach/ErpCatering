<form action="" id="formAsistenciaSalida" method="post">    
    <div class="row">
        @csrf   
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-4">
                <label for="tipo_asistencia_salida" style="font-weight:bold;margin:0;" class="form-label">TIPO</label>
                <div class="form-check">
                    <input value="AUTOMATICO" class="form-check-input" type="checkbox" value="" id="tipo_asistencia_salida" name="tipo_asistencia_salida">
                    <label class="form-check-label" for="tipo_asistencia_salida">
                        AUTOMÁTICA
                    </label>
                </div>
                <span class="tipo_asistencia_salida_error msgError" style="color: rgb(232, 10, 10);font-weight:bold;margin-bottom:3px;"></span>
            </div>
            
            <div id="hora_salida_container" class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                <label for="hora_salida" id="lbl_hora_salida" style="font-weight:bold;margin:0;" class="form-label required_field">HORA</label>
                <input required type="time" id="hora_salida" class="form-control" name="hora_salida">
                <span class="hora_salida_error msgError" style="color: rgb(232, 10, 10);font-weight:bold;"></span>
            </div>

         
           
    </div>
</form> 