@extends('layouts.layout')
@section('title-page')
    <i class="fa-solid fa-tractor" style="color:rgb(0, 68, 255);"></i> ASIGNAR MAQUINARIA 
@endsection

@section('section-page')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
        <h6 style="font-weigth:bold;">
            <i class="fa-solid fa-diagram-project" style="color:rgb(0, 68, 255);"></i> PROYECTO:
            <span>{{$proyecto->nombre}}</span>
        </h6>
    </div>
    <div class="card-body">
        @include('registros.proyectos.forms.form_asignar_maquinaria')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
    
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formAsignarMaquinaria">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>
    let     dtMaquinariasLibres        =   null;
    const   lstMaquinariasAsignadas    =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        iniciarDataTableMaquinariasLibres();
        setLstMaquinariasAsignadas();
        events();
    })

    function events(){
        document.querySelector('#formAsignarMaquinaria').addEventListener('submit',(e)=>{
            e.preventDefault();
            asignarMaquinaria();
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('registros.proyecto.index')}}';
                window.location.href    =   rutaIndex;
            }
        })

        document.addEventListener('change',(e)=>{
            if(e.target.classList.contains('chkMaquinariaLibre')){
                const maquinaria_id    =   e.target.getAttribute('data-maquinaria-id');
                const marcado       =   e.target.checked;

                const indiceMaquinaria =   lstMaquinariasAsignadas.findIndex((u)=>{
                    return u == maquinaria_id;
                })

                if(marcado){
                  if (indiceMaquinaria === -1) {
                    lstMaquinariasAsignadas.push(maquinaria_id);
                  }
                }else{
                    console.log('quitando',indiceMaquinaria);
                    if(indiceMaquinaria !== -1){
                        lstMaquinariasAsignadas.splice(indiceMaquinaria,1);
                    }
                }
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

    function setLstMaquinariasAsignadas(){
        const maquinariasAsignadas =   @json($idsAsignados);
        maquinariasAsignadas.forEach((id)=>{
            lstMaquinariasAsignadas.push(id);
        })
    }

    function iniciarDataTableMaquinariasLibres(){
        dtMaquinariasLibres  =   new DataTable('#table_maquinarias_libres',{
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


    

    function asignarMaquinaria(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ASIGNAR LA MAQUINARIA AL PROYECTO?",
        text: "Confirmar!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
          
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formData                  =   new FormData();
            const urlAsignarMaquinaria      =   @json(route('registros.proyecto.asignarMaquinariaStore'));
            const proyecto_id               =   @json($proyecto_id);

            formData.append('proyecto_id',proyecto_id);
            formData.append('lstMaquinariasAsignadas',JSON.stringify(lstMaquinariasAsignadas));

            Swal.fire({
                title: 'Cargando...',
                html: 'Asignando Maquinaria al proyecto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlAsignarMaquinaria, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                        body: formData
                                    });

                const   res =   await response.json();
                
                console.log(res);
                
               
                if(res.success){
                    const proyecto_index     =   @json(route('registros.proyecto.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   proyecto_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ASIGNAR MAQUINARIA AL PROYECTO');
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

</script>


