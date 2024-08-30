@extends('layouts.layout')
@section('title-page')
    EDITAR USUARIO
@endsection

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos del Usuario<i class="fa-solid fa-user"></i></h6>
    </div>
    <div class="card-body">
       @include('herramientas.usuarios.forms.form_edit_usuario')
    </div>
    <div class="card-footer">

        <div class="col-12" style="display:flex;justify-content:flex-end;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formActualizarUsuario">
                <i class="fa-solid fa-floppy-disk"></i> ACTUALIZAR
            </button>
        </div>
        <div class="col-12">
            <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        </div>
        
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
        document.querySelector('#formActualizarUsuario').addEventListener('submit',(e)=>{
            e.preventDefault();
            
            actualizarUsuario();
        })

        //======== BTN VER CONTRASEÑA =========
        document.addEventListener('click',(e)=>{

            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('herramientas.usuario.index')}}';
                window.location.href    =   rutaIndex;
            }

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
        $('.select_2_form').select2({
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        });
    }

    function actualizarUsuario(){

        const usuario   =   @json($usuario);

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ACTUALIZAR EL USUARIO?",
        text: `USUARIO: ${usuario.name} - CORREO: ${usuario.email}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ACTUALIZAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion();
            
            Swal.fire({
                title: 'Cargando...',
                html: 'Actualizando usuario...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {

                const formActualizarUsuario =   document.querySelector('#formActualizarUsuario');
                const formData              =   new FormData(formActualizarUsuario);
                const token                 =   document.querySelector('input[name="_token"]').value;
                const id                    =   @json($usuario->id);
                let urlUpdateUsuario        =   `{{ route('herramientas.usuario.update', ['id' => ':id']) }}`;
                urlUpdateUsuario            =   urlUpdateUsuario.replace(':id', id);

                const response  =   await fetch(urlUpdateUsuario, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                            'X-HTTP-Method-Override': 'PUT' 
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
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   usuario_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
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


