@extends('layouts.layout')
@section('title-page')
    EDITAR MODALIDAD PAGO
@endsection

@section('section-page')


<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos de la Maquinaria<i class="fa-solid fa-user"></i></h6>
    </div>
    <div class="card-body">
        @include('registros.modalidad_pago.forms.form_edit_modalidad_pago')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formActualizarModalidadPago">
                <i class="fa-solid fa-floppy-disk"></i> ACTUALIZAR
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

        document.querySelector('#formActualizarModalidadPago').addEventListener('submit',(e)=>{
            e.preventDefault();
            actualizarMaquinaria();
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

    function actualizarMaquinaria(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ACTUALIZAR LA MODALIDAD DE PAGO?",
        text: "Se actualizaran los datos de la modalidad de pago!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ACTUALIZAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formActualizarModalidadPago  =   document.querySelector('#formActualizarModalidadPago');
            const formData                  =   new FormData(formActualizarModalidadPago);

            Swal.fire({
                title: 'Cargando...',
                html: 'Actualizando modalidad de pago...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const id                    =   @json($modalidad_pago->id);
                let urlUpdateModalidadPago  =   `{{ route('registros.modalidad_pago.update', ['id' => ':id']) }}`;
                urlUpdateModalidadPago      =   urlUpdateModalidadPago.replace(':id', id);

                const response  =   await fetch(urlUpdateModalidadPago, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                            'X-HTTP-Method-Override': 'PUT' 
                                        },
                                        body: formData
                                    });

                const   res =   await response.json();
                
                if(response.status === 422){
                    if('errors' in res){
                        pintarErroresValidacion(res.errors);
                    }
                    Swal.close();
                    return;
                }
                
                if(res.success){
                    const modalidad_pago_index     =   @json(route('registros.modalidad_pago.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   modalidad_pago_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR MODALIDAD_PAGO');
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


