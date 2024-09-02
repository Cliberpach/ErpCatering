<div class="modal fade" id="mdlAsignarProyecto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Asignar proyecto</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('registros.almacenes.forms.form_asignar_proyecto')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary btnAsignarProyecto" type="submit" form="formAsignarProyecto">
            <i class="fa-solid fa-floppy-disk"></i> Asignar
        </button>
        </div>
      </div>
    </div>
</div>


<script>
    let almacen_asignar_proyecto    =   null;

    function eventsMdlAsignarProyecto(){
        document.querySelector('#formAsignarProyecto').addEventListener('submit',(e)=>{
            e.preventDefault();
            asignarProyecto();
        })
    }

    function openMdlAsignarProyecto(almacen_id){
        almacen_asignar_proyecto    =   almacen_id;

        if(!almacen_asignar_proyecto){
            toastr.error('ERROR AL ACCEDER AL ALMACÉN QUE SE DESEA EDITAR');
            return;
        }

        $('#mdlAsignarProyecto').modal('show');
    }

    function asignarProyecto(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ASIGNAR EL PROYECTO?",
        text: "Se asignará el proyecto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ASIGNAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion();
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formAsignarProyecto       =   document.querySelector('#formAsignarProyecto');
            const formData                  =   new FormData(formAsignarProyecto);

            Swal.fire({
                title: 'Cargando...',
                html: 'Asignando proyecto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlAsignarProyecto      =   `{{ route('registros.almacen.asignarProyecto', ['id' => ':id']) }}`;
                urlAsignarProyecto          =   urlAsignarProyecto.replace(':id', almacen_asignar_proyecto);

                const response  =   await fetch(urlAsignarProyecto, {
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
                    dtAlmacenes.draw();
                    $('#mdlAsignarProyecto').modal('hide');
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ASIGNAR PROYECTO');
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