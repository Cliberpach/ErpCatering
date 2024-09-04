<div class="modal fade" id="mdlAsignarSupervisor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Asignar Supervisor</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('registros.proyectos.forms.form_asignar_supervisor')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary btnAsignarSupervisor" type="submit" form="formAsignarSupervisor">
            <i class="fa-solid fa-floppy-disk"></i> Asignar
        </button>
        </div>
      </div>
    </div>
</div>


<script>
    let proyecto_asignar_supervisor    =   null;

    function eventsMdlAsignarSupervisor(){
        document.querySelector('#formAsignarSupervisor').addEventListener('submit',(e)=>{
            e.preventDefault();
            asignarSupervisor();
        })
    }

    async function openMdlAsignarSupervisor(proyecto_id){
        mostrarAnimacion1();
        proyecto_asignar_supervisor    =   proyecto_id;

        if(!proyecto_asignar_supervisor){
            toastr.error('ERROR AL ACCEDER AL PROYECTO QUE SE DESEA EDITAR');
            return;
        }

        const lstSupervisores   =   await getSupervisores();

        if(lstSupervisores){
            pintarSupervisores(lstSupervisores);
            $('#mdlAsignarSupervisor').modal('show');
        }
        ocultarAnimacion1();

    }

    function pintarSupervisores(lstSupervisores){
        // Limpiar el select antes de añadir nuevos supervisores
        $('#supervisor').empty();


        lstSupervisores.forEach((supervisor) =>{
            $('#supervisor').append(new Option(supervisor.name, supervisor.id));
        });

        // Refrescar el select2 para que tome los cambios
        $('#supervisor').trigger('change');
    }

    async function getSupervisores(){
        try {
            const urlGetSupervisores      =   `{{ route('herramientas.usuario.getSupervisores') }}`;
            const token                     =   document.querySelector('input[name="_token"]').value;

            const response  =   await fetch(urlGetSupervisores, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                        }
                                    });

            const   res =   await response.json();
                                
        
            if(res.success){
                    
                toastr.success(res.message,'OPERACIÓN COMPLETADA');
                return res.supervisores;

            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR');
                return null;
            }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN OBTENER SUPERVISORES');
                return null;
            }
    }

    function asignarSupervisor(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ASIGNAR EL SUPERVISOR?",
        text: "Se asignará el supervisor al proyecto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ASIGNAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion();
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formAsignarSupervisor     =   document.querySelector('#formAsignarSupervisor');
            const formData                  =   new FormData(formAsignarSupervisor);

            Swal.fire({
                title: 'Cargando...',
                html: 'Asignando supervisor...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlAsignarSupervisor      =   `{{ route('registros.proyecto.asignarSupervisor', ['id' => ':id']) }}`;
                urlAsignarSupervisor          =   urlAsignarSupervisor.replace(':id', proyecto_asignar_supervisor);

                const response  =   await fetch(urlAsignarSupervisor, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                            'X-HTTP-Method-Override': 'PATCH' 
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
                    dtProyectos.draw();
                    $('#mdlAsignarSupervisor').modal('hide');
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ASIGNAR SUPERVISOR');
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