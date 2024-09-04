<div class="modal fade" id="mdlCreateMarca" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar Marca</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('reutilizables.modals.marcas.forms.form_create_marca')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary btnRegistrarMarca" type="submit" form="formRegistrarMarca">
            <i class="fa-solid fa-floppy-disk"></i> Registrar
        </button>
        </div>
      </div>
    </div>
</div>


<script>

    function eventsMdlCreateMarca(){
        document.querySelector('#formRegistrarMarca').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarMarca();
        })

        $('#mdlCreateMarca').on('hidden.bs.modal', function () {
           
            //======= RESETAER FORMULARIO ======
            const formRegistrarMarca    =   document.querySelector('#formRegistrarMarca');
            formRegistrarMarca.reset();

            limpiarErroresValidacion('msgErrorMarca');

        });
    }

    function openMdlNuevaMarca(){
        $('#mdlCreateMarca').modal('show');
    }

    function registrarMarca(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR LA MARCA?",
        text: "Se creará una nueva marca!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgErrorMarca');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formRegistrarMarca        =   document.querySelector('#formRegistrarMarca');
            const formData                  =   new FormData(formRegistrarMarca);
            const urlRegistrarMarca         =   @json(route('registros.marca.store'));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nueva marca...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarMarca, {
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
                        pintarErroresValidacionMarca(res.errors);
                    }
                    Swal.close();
                    return;
                }
                
                if(res.success){
                    //======== TRAER LISTADO DE MARCAS ACTUALIZADO =====
                    const lstMarcasActualizadas =   await getMarcasActualizadas();

                    //========= REPINTAR SELECT2 DE MARCAS ========
                    pintarSelect2Marcas(lstMarcasActualizadas);
                    console.log('lst marcas');
                    console.log(lstMarcasActualizadas);
                    $('#mdlCreateMarca').modal('hide');
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR MARCA');
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

    function pintarSelect2Marcas(lstMarcas){
        $('#marca').empty();

        $('#marca').append('<option></option>');

        lstMarcas.forEach(function(marca) {
            $('#marca').append(
                $('<option></option>').val(marca.id).text(marca.descripcion)
            );
        });

        $('#marca').select2({
            allowClear: true,
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        });

        const lastId    =   lstMarcas[lstMarcas.length -1].id;

        if (lastId) {
            $('#marca').val(lastId).trigger('change'); 
        }
    }


    function pintarErroresValidacionMarca(objErroresValidacion){
        for (let clave in objErroresValidacion) {
            const pError        =   document.querySelector(`.${clave}_error_marca`);
            pError.textContent  =   objErroresValidacion[clave][0];
        }
    }

    async function getMarcasActualizadas(){
        try {
            toastr.clear();
            const token                     =   document.querySelector('input[name="_token"]').value;
            const urlGetListMarcas          =   @json(route('registros.marca.getListMarcas'));

            const response  =   await fetch(urlGetListMarcas, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                    });

            const   res     =   await response.json();
                
            if(res.success){
                
                toastr.clear();
                toastr.info(res.message,'MARCAS OBTENIDAS');
                return res.lstMarcas;
                
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR');
                return null;
            }
 
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN OBTENER MARCAS');
                return null;
            }
    }


</script>