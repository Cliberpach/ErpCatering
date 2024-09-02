<form action="" id="formAsignarProyecto" method="post">    
    <div class="row">
        @csrf   
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pb-2">
            <label class="mb-2" for="proyecto" style="font-weight: bold;">
                PROYECTO
            </label>
            <select name="proyecto" class="form-select select2_form" id="proyecto" data-placeholder="Seleccionar" >
                <option></option>
                @foreach ($proyectos as $proyecto)
                    <option
                     value="{{$proyecto->id}}">{{$proyecto->nombre}}</option>
                @endforeach
            </select>
            <span class="proyecto_error msgError"  style="color:red;"></span>
        </div>     
    </div>
</form> 