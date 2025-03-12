<form action="" id="formActualizarProducto" method="post">    
    <div class="row">
            @csrf      
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="nombre" style="font-weight: bold;" class="required_field mb-2">Nombre</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-file-signature"></i>                   
                    </span>
                    <input value="{{$producto->nombre}}" required id="nombre" maxlength="260"  name="nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="nombre_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="codigo_barras" class="mb-2" style="font-weight: bold;">Código de Barras</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-barcode"></i>                   
                    </span>
                    <input  id="codigo_barras" maxlength="20"  name="codigo_barras" type="text" class="form-control" placeholder="Código de Barras" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="codigo_barras_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="codigo_interno" class="mb-2" style="font-weight: bold;">Código Interno</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-key"></i>                  
                    </span>
                    <input  id="codigo_interno" maxlength="20"  name="codigo_interno" type="text" class="form-control" placeholder="Código Interno" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="codigo_interno_error msgError"  style="color:red;"></span>
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
                        @if ($producto->categoria_id === $categoria->id)
                            selected
                        @endif
                        value="{{$categoria->id}}">{{$categoria->descripcion}}</option>
                    @endforeach
                </select>
                <span class="categoria_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required_field mb-2" for="marca" style="font-weight: bold;">MARCA</label>
                <i class="fa-solid fa-plus btn btn-primary" style="border-radius: 90%;padding-right:8px;padding-left:8px;" onclick="openMdlNuevaMarca();"></i>
                <select required name="marca" required class="form-select select2_form" id="marca" data-placeholder="Seleccionar" >
                    <option></option>
                    @foreach ($marcas as $marca)
                        <option
                            @if ($marca->descripcion === 'NACIONAL')
                                selected
                            @endif
                            @if ($producto->marca_id === $marca->id)
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
                        @if ($producto->unidad_medida_id === $unidad_medida->id)
                            selected
                        @endif
                         value="{{$unidad_medida->id}}">{{$unidad_medida->descripcion.' - '.$unidad_medida->simbolo}}</option>
                    @endforeach
                </select>
                <span class="unidad_medida_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="precio" style="font-weight: bold;" class="required_field mb-2">Precio</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-money-check-dollar"></i>                  
                    </span>
                    <input value="{{$producto->precio}}" required id="precio" maxlength="20"  name="precio" type="text" class="form-control numero-input" placeholder="Precio" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="precio_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="stock_minimo" style="font-weight: bold;" class="required_field mb-2">Stock Mínimo</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-layer-group"></i>                 
                    </span>
                    <input value="{{$producto->stock_minimo}}" required id="stock_minimo" maxlength="20"  name="stock_minimo" type="text" class="form-control numero-input" placeholder="Stock mínimo" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="stock_minimo_error msgError"  style="color:red;"></span>
            </div>
           
    </div>
</form>