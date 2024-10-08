<form action="" id="formActualizarProyecto" method="post">    
    <div class="row">
            @csrf      
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="nombre" class="required_field mb-2" style="font-weight: bold;">Nombre</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-file-signature"></i>                   
                    </span>
                    <input value="{{$proyecto->nombre}}" required id="nombre" maxlength="260"  name="nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="nombre_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2 mb-3">
                <label for="costo" class="required_field mb-2" style="font-weight: bold;">Costo</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-money-check-dollar"></i>                  
                    </span>
                    <input value="{{$proyecto->costo}}" value="1.00" required id="costo" maxlength="20"  name="costo" type="text" class="form-control inputDecimalPositivo costo" placeholder="Precio" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="costo_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2 mb-3">
                <label for="avance_costo" class="required_field mb-2" style="font-weight: bold;">Avance Costo</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-money-check-dollar"></i>                  
                    </span>
                    <input value="{{$proyecto->avance_costo}}" value="0.00" required id="avance_costo" maxlength="20"  name="avance_costo" type="text" class="form-control inputDecimalPositivo avance_costo" placeholder="Precio" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="avance_costo_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2 mb-3">
                <label for="diferencia" class="required_field mb-2" style="font-weight: bold;">Diferencia</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-money-check-dollar"></i>                  
                    </span>
                    <input value="{{$proyecto->diferencia}}" readonly value="1.00" required id="diferencia" maxlength="20"  name="diferencia" type="text" class="form-control inputDecimal" placeholder="Precio" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="diferencia_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2 mb-3">
                <label for="direccion" class="required_field mb-2" style="font-weight: bold;">Dirección</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-map-location-dot"></i>                 
                    </span>
                    <input value="{{$proyecto->direccion}}" required id="direccion" maxlength="200"  name="direccion" type="text" class="form-control" placeholder="Dirección" aria-label="Username" aria-describedby="basic-addon1">
                </div>                 
                <span class="direccion_error msgError"  style="color:red;"></span>
            </div>   
    </div>
</form> 