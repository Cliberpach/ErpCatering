@extends('layouts.layout')
@section('title-page')
    EDITAR MAQUINARIA
@endsection

@section('section-page')
@include('reutilizables.modals.tabla_general_detalle.mdl_create_tabla_general_detalle', 
['titulo'           => 'Tipos de gasto',
'tabla_general_id'  =>  2,
'select_2_id'       =>  'tipo_gasto'])

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos de la Maquinaria<i class="fa-solid fa-user"></i></h6>
    </div>
    <div class="card-body">
        @include('registros.maquinarias.forms.form_edit_maquinaria')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formActualizarMaquinaria">
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
        eventsMdlCreateTablaGeneralDetalle();

        document.querySelector('#formActualizarMaquinaria').addEventListener('submit',(e)=>{
            e.preventDefault();
            actualizarMaquinaria();
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('registros.maquinaria.index')}}';
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
        title: "DESEA ACTUALIZAR LA MAQUINARIA?",
        text: "Se actualizaran los datos de la maquinaria!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ACTUALIZAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formActualizarMaquinaria  =   document.querySelector('#formActualizarMaquinaria');
            const formData                  =   new FormData(formActualizarMaquinaria);

            Swal.fire({
                title: 'Cargando...',
                html: 'Actualizando maquinaria...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const id                    =   @json($maquinaria->id);
                let urlUpdateMaquinaria     =   `{{ route('registros.maquinaria.update', ['id' => ':id']) }}`;
                urlUpdateMaquinaria         =   urlUpdateMaquinaria.replace(':id', id);

                const response  =   await fetch(urlUpdateMaquinaria, {
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
                    const maquinaria_index     =   @json(route('registros.maquinaria.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   maquinaria_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR MAQUINARIA');
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

</script>


