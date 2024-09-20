<form action="" id="formAsistenciaEntrada" method="post">    
    <div class="row">
        @csrf   
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-4">
                <label for="tipo_asistencia" style="font-weight:bold;margin:0;" class="form-label">TIPO</label>
                <div class="form-check">
                    <input value="AUTOMATICO" class="form-check-input" type="checkbox" value="" id="tipo_asistencia" name="tipo_asistencia">
                    <label class="form-check-label" for="tipo_asistencia">
                        AUTOMÁTICA
                    </label>
                </div>
                <span class="tipo_asistencia_error msgError" style="color: rgb(232, 10, 10);font-weight:bold;margin-bottom:3px;"></span>
            </div>
            
            <div id="hora_entrada_container" class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                <label for="hora_entrada" id="lbl_hora_entrada" style="font-weight:bold;margin:0;" class="form-label required_field">HORA</label>
                <input required type="time" id="hora_entrada" class="form-control" name="hora_entrada">
                <span class="hora_entrada_error msgError" style="color: rgb(232, 10, 10);font-weight:bold;"></span>
            </div>

            <div>
                <label for="img_asistencia_entrada" style="font-weight:bold;" class="form-label">IMAGEN OPCIONAL</label> <i class="fa-solid fa-trash-can btn btn-danger btnSetImageDefault"></i>
                <input name="img_asistencia_entrada" class="form-control form-control-sm" id="img_asistencia_entrada" type="file" accept="image/*">
            </div>
            <span class="img_asistencia_entrada_error msgError"  style="color:red;"></span>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pb-2 d-flex justify-content-center">
                
                <div id="img_preview_container" style="overflow-x:hidden;overflow-y:hidden;heigth:310px;width:100%;border: 2px dashed #ddd; border-radius: 10px; padding: 10px; text-align: center;display:flex;align-items:center;justify-content:center;">
                    <img class="imgShowLightBox" src="{{asset('img/img_default.png')}}" id="img_vista_previa" style="height: 260px; object-fit: cover;cursor:pointer;">
                </div>
            
            </div>     
    </div>
</form> 