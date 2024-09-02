@extends('layouts.layout')
@section('title-page')
    REGISTRAR PROYECTO
@endsection

@section('section-page')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos del Proyecto<i class="fa-solid fa-toolbox"></i></h6>
    </div>
    <div class="card-body">
        @include('registros.proyectos.forms.form_create_proyecto')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formRegistrarProyecto">
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
        document.querySelector('#formRegistrarProyecto').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarProyecto();
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('registros.proyecto.index')}}';
                window.location.href    =   rutaIndex;
            }
        })

        //=========== AL ESCRIBIR EN INPUT COSTO O AVANCE COSTO =======
        document.addEventListener('input',(e)=>{
            if(e.target.classList.contains('costo') || e.target.classList.contains('avance_costo')){
                calcularDiferencia();
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

    function calcularDiferencia(){
        let costo         =   document.querySelector('#costo').value ? document.querySelector('#costo').value : 0;
        let avance_costo  =   document.querySelector('#avance_costo').value ? document.querySelector('#avance_costo').value : 0;

        costo           =   parseFloat(costo);
        costo           =   Math.trunc(costo * 100) / 100;
        avance_costo    =   parseFloat(avance_costo);
        avance_costo    =   Math.trunc(avance_costo * 100) / 100;


        if (isNaN(costo) && isNaN(avance_costo)) {
            toastr.clear();
            toastr.error('EL COSTO Y AVANCE COSTO NO SON VALORES NUMÉRICOS');
            document.querySelector('#diferencia').value =   0;
            return;
        }

     
        let diferencia  =   costo - avance_costo;
        diferencia      =   diferencia.toFixed(2);

        document.querySelector('#diferencia').value =   diferencia;
    }

    function registrarProyecto(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR EL PROYECTO?",
        text: "Se creará un nuevo proyecto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion();
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formRegistrarProyecto     =   document.querySelector('#formRegistrarProyecto');
            const formData                  =   new FormData(formRegistrarProyecto);
            const urlRegistrarProyecto      =   @json(route('registros.proyecto.store'));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nuevo proyecto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarProyecto, {
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
                    const proyecto_index     =   @json(route('registros.proyecto.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   proyecto_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR PROYECTO');
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

    function limpiarErroresValidacion(){
        const lstEtiquetasErrors    =   document.querySelectorAll('.msgError');
        lstEtiquetasErrors.forEach((etiqueta)=>{
            etiqueta.textContent    =   '';
        })
    }

</script>


