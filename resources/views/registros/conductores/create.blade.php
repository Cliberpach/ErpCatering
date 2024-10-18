@extends('layouts.layout')
@section('title-page')
    REGISTRAR CONDUCTOR
@endsection
@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('conductor-active', 'active')


@section('section-page')
<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos del Conductor<i class="fa-solid fa-user"></i></h6>
    </div>
    <div class="card-body">
        @include('registros.conductores.forms.form_create_conductor')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formRegistrarConductor">
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

        document.querySelector('#formRegistrarConductor').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarConductor();
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('registros.conductor.index')}}';
                window.location.href    =   rutaIndex;
            }
        })

        //======= CONSULTAR API DOCUMENTO DNI ========
        document.querySelector('#btn_consultar_documento').addEventListener('click',()=>{
            const dni               =   document.querySelector('#nro_documento').value;
            const tipo_documento    =   document.querySelector('#tipo_documento').value;
            toastr.clear();

            if(tipo_documento != 1){
                toastr.error('SOLO SE PUEDE CONSULTAR TIPO DE DOCUMENTO DNI');
                return;
            }

            if(dni.length != 8){
                toastr.error('NRO DE DNI DEBE CONTAR CON 8 DÍGITOS');
                return;
            }

            consultarDocumento(dni);

        })

      
        //========== PERMITIR SOLO FORMATO DE CELULAR O TELEFONO ======
        document.querySelector('#telefono').addEventListener('input',(e)=>{
            const input = e.target;
            const maxLength = 20;
            
            // Expresión regular para validar números de teléfono internacionales
            const validPattern = /^\+?[0-9]*$/;

            // Reemplaza cualquier carácter que no sea un dígito o "+"
            let value = input.value.replace(/[^0-9+]/g, '');

            // Asegúrate de que el símbolo '+' esté al principio
            if (value.startsWith('+')) {
                value = '+' + value.slice(1).replace(/^\+/, '');
            } else {
                value = value.replace(/^\+/, '');
            }

            // Limita el valor a 20 caracteres
            if (value.length > maxLength) {
                value = value.slice(0, maxLength);
            }

            // Actualiza el valor del input
            input.value = value;
        })

        //===== PERMITIR SOLO NUMEROS ========
        document.querySelector('#nro_documento').addEventListener('input', (e) => {
            const input = e.target;

            input.value = input.value.replace(/\D/g, '');
        });
    }

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        } ); 
    }

    function registrarConductor(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR EL CONDUCTOR?",
        text: "Se creará un nuevo conductor!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formRegistrarConductor    =   document.querySelector('#formRegistrarConductor');
            const formData                  =   new FormData(formRegistrarConductor);
            const urlRegistrarConductor     =   @json(route('registros.conductor.store'));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nuevo conductor...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarConductor, {
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
                    const conductor_index     =   @json(route('registros.conductor.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   conductor_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR CONDUCTOR');
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
            const pError        =   document.querySelector(`.${clave}_error`);
            pError.textContent  =   objErroresValidacion[clave][0];
        }
    }

    //======== CHANGE TIPO DOCUMENTO ======
    function changeTipoDoc(params) {
        const tipo_documento        =   document.querySelector('#tipo_documento').value;
        const inputNroDoc           =   document.querySelector('#nro_documento');
        const btnConsultarDocumento =   document.querySelector('#btn_consultar_documento');
        
        //======== DNI =======
        if(tipo_documento == 1){
            inputNroDoc.value               =   '';
            inputNroDoc.readOnly            =   false;
            inputNroDoc.maxLength           =   8;
            btnConsultarDocumento.disabled  =   false;
        }

        //====== CARNET EXTRANJERÍA =====
        if(tipo_documento == 3){
            inputNroDoc.value               =   '';
            inputNroDoc.readOnly            =   false;
            inputNroDoc.maxLength           =   20;
            btnConsultarDocumento.disabled  =   true;
        }
    }

    //======= CONSULTAR DOCUMENTO IDENTIDAD =====
    async function consultarDocumento(dni){
        mostrarAnimacion1();
        try {
            const token     =   document.querySelector('input[name="_token"]').value;
            const urlApiDni = `/colaboradores/consultarDni/${encodeURIComponent(dni)}`;

            const response  =   await fetch(urlApiDni, {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': token 
                                    },
                                });

            const   res =   await response.json();

            if(res.success){
                setDatosDni(res.data.data);
                toastr.info(res.message);
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR AL CONSULTAR DNI');
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN CONSULTAR DNI');
        }finally{
            ocultarAnimacion1();
        }
    }

    function setDatosDni(data){
        const nombre_completo   =   `${data.nombres} ${data.apellido_paterno} ${data.apellido_materno}`;

        document.querySelector('#nombre').value     =   nombre_completo;
    }

</script>


