@extends('layouts.layout')
@section('title-page')
    CREAR USUARIO
@endsection

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos del Usuario<i class="fa-solid fa-user"></i></h6>
    </div>
    <div class="card-body">
        <form action="" id="formRegistrarUsuario" method="post">    
            <div class="row">
                    @csrf       
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                        <label for="nombre" class="required_field" style="font-weight: bold;">Nombre</label>
                        <input maxlength="255" name="nombre" required type="text" class="form-control" placeholder="Usuario">
                        <span class="nombre_error msgError"  style="color:red;"></span>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                        <label for="correo" class="required_field" style="font-weight: bold;">Correo</label>
                        <input maxlength="255" name="correo" id="correo" required type="email" class="form-control" placeholder="Correo">
                        <span class="correo_error msgError"  style="color:red;"></span>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                        <label for="colaborador" class="required_field" style="font-weight: bold;">Colaborador</label>
                        <select name="colaborador" required class="form-select" id="colaborador" data-placeholder="Seleccionar">
                            <option></option>
                            @foreach ($colaboradores as $colaborador)
                                <option value="{{$colaborador->id}}">
                                    {{$colaborador->nombre.' - '.$colaborador->tipo_documento_nombre.':'.$colaborador->nro_documento}}
                                </option>
                            @endforeach
                            
                        </select>
                        <span class="colaborador_error msgError"  style="color:red;"></span>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                        <label for="password" class="required_field" style="font-weight: bold;">Contraseña</label>
                        <div class="input-group mb-3">
                            <button style="width:50px;" class="btn btn-primary btn_ver_password password_oculto" type="button" id="button-addon1">
                                <i class="fa-solid fa-eye-slash"></i>
                            </button>
                            <input maxlength="50" type="password" id="password" name="password" class="form-control" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
                        </div>
                        <span class="password_error msgError" style="color:red;"></span>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                        <label for="repetir_password" class="required_field" style="font-weight: bold;">Repetir contraseña</label>
                        <div class="input-group mb-3">
                            <button style="width:50px;"  class="btn btn-primary btn_ver_repetir_password password_oculto" type="button" id="button-addon1">
                                <i class="fa-solid fa-eye-slash"></i>
                            </button>
                            <input maxlength="50" type="password" id="repetir_password" name="repetir_password" class="form-control" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
                        </div>
                        <span class="repetir_password_error msgError" style="color:red;"></span>
                    </div>
            </div>
        </form> 
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        <button class="btn btn-primary" type="submit" form="formRegistrarUsuario">
            <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
        </button>
    </div>
</div>
<!-- end card -->
@endsection


<script>
    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        events();
    })

    function events(){
        document.querySelector('#formRegistrarUsuario').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarUsuario();
        })

        //======== BTN VER CONTRASEÑA =========
        document.addEventListener('click',(e)=>{

            if (e.target.closest('.btn_ver_password')) {
                
                const btnVerPassword   =   e.target.closest('.btn_ver_password');
                btnVerPassword.classList.toggle('password_oculto');

                if(btnVerPassword.classList.contains('password_oculto')){
                    //======== OCULTAR PASSWORD =======
                    document.querySelector('#password').type    =   'password';
                     //===== CAMBIANDO ICONO =====
                     const icon  =   btnVerPassword.children[0];

                    icon.classList.add('hide-transition');
                    setTimeout(() => {
                        icon.className = 'fa-solid fa-eye-slash show-transition';
                        icon.classList.remove('hide-transition');
                    }, 300);
                }else{
                    //====== MOSTRAR PASSWORD =======
                    document.querySelector('#password').type    =   'text';
                    //===== CAMBIANDO ICONO =====
                    const icon  =   btnVerPassword.children[0];
                    icon.classList.add('hide-transition');
                    setTimeout(() => {
                        icon.className = 'fa-solid fa-eye show-transition';
                    }, 300); 
                }
            }

            if (e.target.closest('.btn_ver_repetir_password')) {
                
                const btnVerRepetirPassword   =   e.target.closest('.btn_ver_repetir_password');
                btnVerRepetirPassword.classList.toggle('password_oculto');

                if(btnVerRepetirPassword.classList.contains('password_oculto')){
                    //======== OCULTAR PASSWORD =======
                    document.querySelector('#repetir_password').type    =   'password';
                    //===== CAMBIANDO ICONO =====
                    const icon  =   btnVerRepetirPassword.children[0];

                    icon.classList.add('hide-transition');
                    setTimeout(() => {
                        icon.className = 'fa-solid fa-eye-slash show-transition';
                        icon.classList.remove('hide-transition');
                    }, 300);

                }else{
                    //====== MOSTRAR PASSWORD =======
                    document.querySelector('#repetir_password').type    =   'text';
                    //===== CAMBIANDO ICONO =====
                    const icon  =   btnVerRepetirPassword.children[0];
                    icon.classList.add('hide-transition');
                    setTimeout(() => {
                        icon.className = 'fa-solid fa-eye show-transition';
                    }, 300); 
                }
            }

        })
    }

    function iniciarSelect2(){
        $('#colaborador').select2({
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        });
    }

    function registrarUsuario(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR EL USUARIO?",
        text: "Se creará un nuevo usuario!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion();
            const token                 =   document.querySelector('input[name="_token"]').value;
            const formRegistrarUsuario  =   document.querySelector('#formRegistrarUsuario');
            const formData              =   new FormData(formRegistrarUsuario);
            const urlRegistrarUsuario   =   @json(route('herramientas.usuario.store'));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nuevo usuario...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarUsuario, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': token 
                                    },
                                    body: formData
                                });

                const   res =   await response.json();
                
                console.log(res);
                
                if(response.status === 422){
                    if('errors' in res){
                        pintarErroresValidacion(res.errors);
                    }
                    Swal.close();
                    return;
                }
                

                if(res.success){
                    const usuario_index     =   @json(route('herramientas.usuario.index'));
                    toastr.success(response.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   usuario_index;
                }else{
                    toastr.error(response.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR USUARIO');
                Swal.close();
            }
          

        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire({
            title: "OPERACIÓN CANCELADA",
            text: "NO SE REALIZARON ACCIONES",
            icon: "error"
            });
        }
        });
    }

    function pintarErroresValidacion(objErroresValidacion){
        for (let clave in objErroresValidacion) {
            const pError    =   document.querySelector(`.${clave}_error`);
            pError.textContent  =   objErroresValidacion[clave][0];
        }
    }

    function limpiarErroresValidacion(){
        const lstEtiquetasErrors    =   document.querySelectorAll('.msgError');
        lstEtiquetasErrors.forEach((etiqueta)=>{
            etiqueta.textContent    =   '';
        })
    }

</script>


