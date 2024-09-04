<form action="" id="formAsignarSupervisor" method="post">    
    <div class="row">
        @csrf   
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pb-2">
            <label class="mb-2" for="proyecto" style="font-weight: bold;">
                SUPERVISOR
            </label>
            <select name="supervisor" class="form-select select2_form" id="supervisor" data-placeholder="Seleccionar" >
                <option></option>
                {{-- @foreach ($supervisores as $supervisor)
                    <option
                     value="{{$supervisor->id}}">{{$supervisor->name}}</option>
                @endforeach --}}
            </select>
            <span class="supervisor_error msgError"  style="color:red;"></span>
        </div>     
    </div>
</form> 