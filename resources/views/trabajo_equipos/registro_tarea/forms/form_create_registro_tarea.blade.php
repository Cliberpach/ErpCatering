<form action="" id="formRegistrarTarea" method="post">    
    <div class="row">
            @csrf  
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="proyecto_nombre" class="required_field" style="font-weight: bold;">PROYECTO</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-file-signature"></i>                   
                    </span>
                    <input type="text" name="proyecto_id" value="{{$proyecto->id}}" hidden>
                    <input required readonly value="{{$proyecto->nombre}}" id="proyecto_nombre" maxlength="260"  name="proyecto_nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="proyecto_nombre_error msgError"  style="color:red;"></span>
                <span class="proyecto_id_error msgError"  style="color:red;"></span>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2 mb-3">
                <label class="required_field mb-2" for="maquinaria" style="font-weight: bold;">MAQUINARIA</label>
                <select required name="maquinaria" required class="form-select select2_form" id="maquinaria" data-placeholder="Seleccionar">
                    <option></option>
                    @foreach ($maquinarias as $maquinaria)
                        <option
                        value="{{$maquinaria->maquinaria_id}}">{{$maquinaria->maquinaria_nombre}}</option>
                    @endforeach
                </select>
                <span class="maquinaria_error msgError"  style="color:red;"></span>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2 mb-3">
                <label for="cant_horas_viajes" class="required_field mb-2" style="font-weight: bold;">N° Horas o Viajes</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-regular fa-clock"></i>               
                    </span>
                    <input required id="cant_horas_viajes" maxlength="20"  name="cant_horas_viajes" type="text" class="form-control inputEnteroPositivo" placeholder="N° Horas o Viajes" aria-label="Username" aria-describedby="basic-addon1">             
                </div>                  
                <span class="cant_horas_viajes_error msgError"  style="color:red;"></span>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="observacion" class="mb-2" style="font-weight: bold;">Observacion</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-file-signature"></i>                   
                    </span>
                    <div class="form-floating">
                        <textarea maxlength="300" class="form-control" name="observacion" placeholder="Leave a comment here" id="observacion"></textarea>
                        <label for="observacion">Observacion</label>
                    </div>                
                </div>                  
                <span class="observacion_error msgError"  style="color:red;"></span>
            </div>
   
    </div>
</form> 