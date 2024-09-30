@extends('layouts.layout')
@section('title-page')
    REGISTRAR TAREA
@endsection

@section('plan_proyecto-collapsed', '')
@section('plan_proyecto-expanded', 'true')
@section('plan_proyecto-show', 'show')
@section('tareas-active', 'active')

@section('section-page')
@include('plan_proyecto.tareas.modals.modal_create_subtarea')
@include('plan_proyecto.tareas.modals.modal_edit_subtarea')
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
            <button class="btn btn-primary" type="submit" form="formRegistrarTarea">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>
    let dtSubtareas =   null;
    
    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        iniciarDataTableSubtareas();
        events();
    })

    function events(){
        eventsMdlCreateSubtarea();

        document.querySelector('#formRegistrarTarea').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarTarea();
        })

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

    function iniciarDataTableSubtareas(){
        dtSubtareas  =   new DataTable('#table_subtareas',{
            language: {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "emptyTable": "No hay datos disponibles en la tabla",
                "aria": {
                    "sortAscending": ": activar para ordenar la columna de manera ascendente",
                    "sortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }

    function registrarTarea(){

        if(lstSubtareas.length === 0){
            toastr.error('DEBES CREAR SUBTAREAS PREVIAMENTE!!!');
            return;
        }

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR LA TAREA?",
        text: "Se creará una nueva tarea con subtareas!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgErrorTarea');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formRegistrarTarea        =   document.querySelector('#formRegistrarTarea');
            const formData                  =   new FormData(formRegistrarTarea);
            const urlRegistrarTarea         =   @json(route('plan_proyecto.tarea.store'));
            formData.append('proyecto_id',@json($proyecto->id));
            formData.append('lstSubtareas',JSON.stringify(lstSubtareas));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nueva tarea...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarTarea, {
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
                    const tarea_index       =   @json(route('plan_proyecto.tarea.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   tarea_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR TAREA');
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


