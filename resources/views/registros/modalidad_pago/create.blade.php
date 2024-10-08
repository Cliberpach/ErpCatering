@extends('layouts.layout')
@section('title-page')
    REGISTRAR MODALIDAD DE PAGO
@endsection

@section('section-page')


<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos de la Modalidad de Pago <i class="fa-solid fa-money-bill"></i></h6>
    </div>
    <div class="card-body">
        @include('registros.modalidad_pago.forms.form_create_modalidad_pago')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formRegistrarModalidadPago">
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
        
        document.querySelector('#formRegistrarModalidadPago').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarModalidadPago();
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('registros.modalidad_pago.index')}}';
                window.location.href    =   rutaIndex;
            }
        })

    }

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        } );
        
    }

    function registrarModalidadPago(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR LA MODALIDAD DE PAGO?",
        text: "Se creará una nueva modalidad de pago!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            const token                         =   document.querySelector('input[name="_token"]').value;
            const formRegistrarModalidadPago    =   document.querySelector('#formRegistrarModalidadPago');
            const formData                      =   new FormData(formRegistrarModalidadPago);
            const urlRegistrarModalidadPago     =   @json(route('registros.modalidad_pago.store'));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nueva modalidad de pago...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarModalidadPago, {
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
                    const maquinaria_index     =   @json(route('registros.modalidad_pago.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   maquinaria_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR MODALIDAD DE PAGO');
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

    function changeTipoPago(){
        const tipo          =   document.querySelector('#tipo').value;
        const inputNroDias  =   document.querySelector('#nro_dias');

        inputNroDias.readOnly   =   true;
        inputNroDias.value      =   0;

        if(!tipo){
            toastr.error('EL TIPO DE MODALIDAD DE PAGO NO ES VÁLIDO');
            return;
        }

        if(tipo == 2){
            inputNroDias.readOnly   =   false;
            inputNroDias.value      =   1;
        }
    }

</script>


