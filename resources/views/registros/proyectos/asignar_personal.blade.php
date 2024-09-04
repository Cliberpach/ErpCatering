@extends('layouts.layout')
@section('title-page')
    ASIGNAR PERSONAL
@endsection

@section('section-page')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Asignar Personal<i class="fa-solid fa-toolbox"></i></h6>
    </div>
    <div class="card-body">
        @include('registros.proyectos.forms.form_asignar_personal')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formAsignarPersonal">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>
    let     dtUsuariosLibres        =   null;
    const   lstUsuariosAsignados    =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        iniciarDataTableUsuariosLibres();
        setLstUsuariosAsignados();
        events();
    })

    function events(){
        document.querySelector('#formAsignarPersonal').addEventListener('submit',(e)=>{
            e.preventDefault();
            asignarPersonal();
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('registros.proyecto.index')}}';
                window.location.href    =   rutaIndex;
            }
        })

        document.addEventListener('change',(e)=>{
            if(e.target.classList.contains('chkUsuarioLibre')){
                const usuario_id    =   e.target.getAttribute('data-usuario-id');
                const marcado       =   e.target.checked;
                console.log('usuario marcado o desmarcado',usuario_id);

                const indiceUsuario =   lstUsuariosAsignados.findIndex((u)=>{
                    return u == usuario_id;
                })

                if(marcado){
                  if (indiceUsuario === -1) {
                    lstUsuariosAsignados.push(usuario_id);
                  }
                }else{
                    console.log('quitando',indiceUsuario);
                    if(indiceUsuario !== -1){
                        lstUsuariosAsignados.splice(indiceUsuario,1);
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

    function setLstUsuariosAsignados(){
        const usuariosAsignados =   @json($idsAsignados);
        usuariosAsignados.forEach((id)=>{
            lstUsuariosAsignados.push(id);
        })
    }

    function iniciarDataTableUsuariosLibres(){
        dtUsuariosLibres  =   new DataTable('#table_usuarios_libres',{
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


    

    function asignarPersonal(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ASIGNAR PERSONAL AL PROYECTO?",
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
            const urlAsignarPersonal        =   @json(route('registros.proyecto.asignarPersonalStore'));
            const proyecto_id               =   @json($proyecto_id);

            formData.append('proyecto_id',proyecto_id);
            formData.append('lstUsuariosAsignados',JSON.stringify(lstUsuariosAsignados));

            Swal.fire({
                title: 'Cargando...',
                html: 'Asignando Personal al proyecto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlAsignarPersonal, {
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
                toastr.error(error,'ERROR EN LA PETICIÓN ASIGNAR PERSONAL AL PROYECTO');
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


