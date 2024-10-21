<form action="" id="formRegistrarConductor" method="post">    
    <div class="row">
            @csrf   
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required_field" for="tipo_documento" style="font-weight: bold;">TIPO DOCUMENTO</label>
                <select required name="tipo_documento" required class="form-select select2_form" id="tipo_documento" data-placeholder="Seleccionar" onchange="changeTipoDoc()">
                    <option></option>
                    @foreach ($tipos_documento as $tipo_documento)
                        <option value="{{$tipo_documento->id}}">{{$tipo_documento->descripcion}}</option>
                    @endforeach
                </select>
                <span class="tipo_documento_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="nro_documento" style="font-weight: bold;" class="required_field">Nro Doc</label>
                <div class="input-group mb-3">
                    <button id="btn_consultar_documento" disabled class="btn btn-primary" type="button" id="button-addon1">
                        <i class="fa-solid fa-magnifying-glass" style="color:white;"></i>
                    </button>
                    <input required readonly id="nro_documento" name="nro_documento" type="text" class="form-control" placeholder="Nro de Documento" aria-label="Example text with button addon" aria-describedby="button-addon1">
                </div>                 
                <span class="nro_documento_error msgError"  style="color:red;"></span>
            </div>    
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="nombre" style="font-weight: bold;" class="required_field">Nombres</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/conductor/conductor1.png')}}" alt="coins"/>                    
                    </span>
                    <input required id="nombre" maxlength="150"  name="nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1">
                </div>       
                <span style="color:rgb(0, 89, 255); font-style: italic;">(150 LONGITUD MÁXIMA)</span>                 
                <span class="nombre_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="apellido" style="font-weight: bold;" class="required_field">Apellidos</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/conductor/conductor1.png')}}" alt="coins"/>                    
                    </span>
                    <input required id="apellido" maxlength="150"  name="apellido" type="text" class="form-control" placeholder="Apellidos" aria-label="Username" aria-describedby="basic-addon1">
                </div>       
                <span style="color:rgb(0, 89, 255); font-style: italic;">(150 LONGITUD MÁXIMA)</span>                 
                <span class="apellido_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required_field" for="licencia" style="font-weight: bold;">LICENCIA</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/licencia/licencia1.png')}}" alt="coins"/>                                        
                    </span>
                    <input minlength="9" maxlength="10" required id="licencia" name="licencia" type="text" class="form-control" placeholder="Licencia" aria-label="Username" aria-describedby="basic-addon1">
                </div>            
                <span style="color:rgb(0, 89, 255); font-style: italic;">(9 - 10 CARACTERES)</span>      
                <span class="licencia_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="telefono" style="font-weight: bold;">Teléfono</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <img width="30" height="30" src="{{asset('img/icons/telefono/telefono2.png')}}" alt="coins"/>                                        
                    </span>
                    <input maxlength="20"  id="telefono" name="telefono" type="text" class="form-control" placeholder="Teléfono" aria-label="Username" aria-describedby="basic-addon1">
                </div> 
                <span style="color:rgb(0, 89, 255); font-style: italic;">(20 LONGITUD MÁXIMA)</span>                                 
                <span class="telefono_error msgError"  style="color:red;"></span>
            </div>
    </div>
</form> 