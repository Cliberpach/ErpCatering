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
                        <label for="nombre" style="font-weight: bold;">Nombre</label>
                        <input name="nombre" required type="text" class="form-control" placeholder="Usuario">
                        <span class="nombre_error"  style="color:red;"></span>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                        <label for="correo" style="font-weight: bold;">Correo</label>
                        <input name="correo" id="correo" required type="email" class="form-control" placeholder="Correo">
                        <span class="correo_error"  style="color:red;"></span>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                        <label for="colaborador" style="font-weight: bold;">Colaborador</label>
                        <select name="colaborador" required class="form-select" id="colaborador" data-placeholder="Choose one thing">
                            <option></option>
                            <option value="1">Reactive</option>
                            <option value="2">Solution</option>
                            <option value="3">Conglomeration</option>
                            <option value="4">Algoritm</option>
                            <option value="5">Holistic</option>
                        </select>
                        <span class="colaborador_error"  style="color:red;"></span>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                        <label for="password" style="font-weight: bold;">Contraseña</label>
                        <input name="password" required id="password" type="password" class="form-control" placeholder="Usuario">
                        <span class="password_error" style="color:red;"></span>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                        <label for="repetir_password" style="font-weight: bold;">Repetir contraseña</label>
                        <input name="repetir_password" required id="repetir_password" type="password" class="form-control" placeholder="Usuario">
                        <span class="repetir_password_error" style="color:red;"></span>
                    </div>
            </div>
        </form> 
    </div>
    <div class="row">
        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-primary" type="submit" form="formRegistrarUsuario">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
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
        document.querySelector('#formRegistrarUsuario').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarUsuario();
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
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR CLIENTE');
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


