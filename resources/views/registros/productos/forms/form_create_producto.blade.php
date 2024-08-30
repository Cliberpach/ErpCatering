<form action="" id="formRegistrarProducto" method="post">    
    <div class="row">
            @csrf      
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="nombre" class="required_field mb-2" style="font-weight: bold;">Nombre</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-file-signature"></i>                   
                    </span>
                    <input required id="nombre" maxlength="260"  name="nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="nombre_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required_field mb-2" for="categoria" style="font-weight: bold;">CATEGORÍA</label>
                <i class="fa-solid fa-plus btn btn-primary" style="border-radius: 90%;padding-right:8px;padding-left:8px;" onclick="openMdlNuevaCategoria();"></i>
                <select required name="categoria" required class="form-select select2_form" id="categoria" data-placeholder="Seleccionar">
                    <option></option>
                    @foreach ($categorias as $categoria)
                        <option
                        @if ($categoria->descripcion === 'PRODUCTO')
                            selected
                        @endif 
                        value="{{$categoria->id}}">{{$categoria->descripcion}}</option>
                    @endforeach
                </select>
                <span class="categoria_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required_field mb-2" for="marca" style="font-weight: bold;">
                    MARCA
                </label>
                <i class="fa-solid fa-plus btn btn-primary" style="border-radius: 90%;padding-right:8px;padding-left:8px;" onclick="openMdlNuevaMarca();"></i>
                <select required name="marca" required class="form-select select2_form" id="marca" data-placeholder="Seleccionar" >
                    <option></option>
                    @foreach ($marcas as $marca)
                        <option
                            @if ($marca->descripcion === 'NACIONAL')
                                selected
                            @endif
                         value="{{$marca->id}}">{{$marca->descripcion}}</option>
                    @endforeach
                </select>
                <span class="marca_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required_field mb-2" for="unidad_medida" style="font-weight: bold;">UNIDAD MEDIDA</label>
                <select required name="unidad_medida" required class="form-select select2_form" id="unidad_medida" data-placeholder="Seleccionar" >
                    <option></option>
                    @foreach ($unidades_medida as $unidad_medida)
                        <option
                        @if ($unidad_medida->simbolo === 'NIU')
                            selected
                        @endif
                         value="{{$unidad_medida->id}}">{{$unidad_medida->descripcion.' - '.$unidad_medida->simbolo}}</option>
                    @endforeach
                </select>
                <span class="unidad_medida_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="precio" class="required_field mb-2" style="font-weight: bold;">Precio</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-money-check-dollar"></i>                  
                    </span>
                    <input value="1.00" required id="precio" maxlength="20"  name="precio" type="text" class="form-control inputDecimalPositivo" placeholder="Precio" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="precio_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="stock" class="required_field mb-2" style="font-weight: bold;">Stock</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-layer-group"></i>                 
                    </span>
                    <input value="0.00" required id="stock" maxlength="20"  name="stock" type="text" class="form-control inputDecimalPositivo" placeholder="Stock" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="stock_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="stock_minimo" class="required_field mb-2" style="font-weight: bold;">Stock Mínimo</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-layer-group"></i>                 
                    </span>
                    <input value="1.00" required id="stock_minimo" maxlength="20"  name="stock_minimo" type="text" class="form-control inputDecimalPositivo" placeholder="Stock mínimo" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="stock_minimo_error msgError"  style="color:red;"></span>
            </div>
           
    </div>
</form> 