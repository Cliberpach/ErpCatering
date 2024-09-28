@extends('layouts.layout')
@section('title-page')
    REGISTRAR TAREA
@endsection

@section('plan_proyecto-collapsed', '')
@section('plan_proyecto-expanded', 'true')
@section('plan_proyecto-show', 'show')
@section('tareas-active', 'active')

@section('section-page')
@include('reutilizables.modals.marcas.mdl_create_marca')
@include('reutilizables.modals.categorias.mdl_create_categoria')
<div class="card-style settings-card-1 mb-30">
    <div class="title mb-3">
        <div class="row mb-5">
            <div class="col-12">
                <h4><i class="fa-solid fa-diagram-project" style="color: rgb(14, 58, 191);"></i> PROYECTO: <span style="margin:0;">{{$proyecto->nombre}}</span></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h6>Datos de la Tarea <i class="fa-solid fa-toolbox"></i></h6>
            </div>
        </div>
    </div>
    <div class="card-body">
        @include('plan_proyecto.tareas.forms.form_create_tarea')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formRegistrarProducto">
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
        eventsMdlCreateMarca();
        eventsMdlCreateCategoria();

        // document.querySelector('#formRegistrarProducto').addEventListener('submit',(e)=>{
        //     e.preventDefault();
        //     registrarProducto();
        // })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('plan_proyecto.tarea.index')}}';
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

    function registrarProducto(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR EL PRODUCTO?",
        text: "Se creará un nuevo producto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formRegistrarProducto     =   document.querySelector('#formRegistrarProducto');
            const formData                  =   new FormData(formRegistrarProducto);
            const urlRegistrarProducto      =   @json(route('registros.producto.store'));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nuevo producto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarProducto, {
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
                    const producto_index     =   @json(route('registros.producto.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   producto_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR PRODUCTO');
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


