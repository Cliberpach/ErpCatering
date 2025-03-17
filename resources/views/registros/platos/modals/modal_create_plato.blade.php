<!-- Modal Crear Plato -->
<div class="modal fade" id="mdlCreatePlato" tabindex="-1" aria-labelledby="modalCrearPlatoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearPlatoLabel">Crear Plato</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           @include('registros.platos.forms.form_create_plato')
        </div>
    </div>
</div>

<script>
    function eventsMdlCreatePlato(){
        document.querySelector('#formRegistrarPlato').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarPlato();
        })

        $('#mdlCreatePlato').on('hidden.bs.modal', function (e) {
            const   formRegistrarPlato    =   document.querySelector('#formRegistrarPlato');
            formRegistrarPlato.reset();
            limpiarErroresValidacion('msgError');
        });

    }

    function openMdlNuevoPlato(){
        $('#mdlCreatePlato').modal('show');
    }

    function registrarPlato(){
        
        Swal.fire({
        title: "DESEA REGISTRAR EL PLATO?",
        text: "Se creará un nuevo plato!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formRegistrarPlato        =   document.querySelector('#formRegistrarPlato');
            const formData                  =   new FormData(formRegistrarPlato);
            const urlRegistrarPlato         =   @json(route('registros.platos.store'));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nuevo Plato...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarPlato, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
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
                    dtPlatos.draw();
                    $('#mdlCreatePlato').modal('hide');
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR CARGO');
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

    function limpiarErroresValidacion() {
        document.querySelectorAll('.msgError').forEach((el) => {
            el.textContent = '';
        });
    }
</script>