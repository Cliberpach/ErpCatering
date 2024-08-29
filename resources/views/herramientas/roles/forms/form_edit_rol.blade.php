<form action="" method="post" id="formActualizarRol">
    @csrf
    <div class="row">
        <div class="col-6">
            <label for="nombre" class="required_field" style="font-weight: bold;">ROL</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fa-solid fa-circle-user"></i>
                </span>
                <input value="{{$rol->name}}" required maxlength="255" type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre del rol" aria-label="Username" aria-describedby="basic-addon1">
                <span class="nombre_error msgError"  style="color:red;"></span>
            </div>
        </div>
    </div>
</form>