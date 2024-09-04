<div class="modal fade" id="mdlCreateCategoria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar Categoría</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('reutilizables.modals.categorias.forms.form_create_categoria')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary btnRegistrarCategoria" type="submit" form="formRegistrarCategoria">
            <i class="fa-solid fa-floppy-disk"></i> Registrar
        </button>
        </div>
      </div>
    </div>
</div>


<script>

    function eventsMdlCreateCategoria(){
        document.querySelector('#formRegistrarCategoria').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarCategoria();
        })

        $('#mdlCreateCategoria').on('hidden.bs.modal', function () {
           
           //======= RESETAER FORMULARIO ======
           const formRegistrarCategoria    =   document.querySelector('#formRegistrarCategoria');
           formRegistrarCategoria.reset();

           limpiarErroresValidacion('msgErrorCategoria');

       });
    }

    function openMdlNuevaCategoria(){
        $('#mdlCreateCategoria').modal('show');
    }

    function registrarCategoria(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR LA CATEGORÍA?",
        text: "Se creará una nueva categoría!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgErrorCategoria');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formRegistrarCategoria        =   document.querySelector('#formRegistrarCategoria');
            const formData                  =   new FormData(formRegistrarCategoria);
            const urlRegistrarCategoria         =   @json(route('registros.categoria.store'));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nueva categoría...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarCategoria, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                        body: formData
                                    });

                const   res =   await response.json();
                                
                if(response.status === 422){
                    if('errors' in res){
                        pintarErroresValidacionCategoria(res.errors);
                    }
                    Swal.close();
                    return;
                }
                
                if(res.success){
                    //======== TRAER LISTADO DE CATEGORIAS ACTUALIZADO =====
                    const lstCategoriasActualizadas =   await getCategoriasActualizadas();

                    //========= REPINTAR SELECT2 DE CATEGORIA ========
                    pintarSelect2Categorias(lstCategoriasActualizadas);
                    console.log('lst categorias');
                    console.log(lstCategoriasActualizadas);
                    $('#mdlCreateCategoria').modal('hide');
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR CATEGORÍA');
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

    function pintarSelect2Categorias(lstCategorias){
        $('#categoria').empty();

        $('#categoria').append('<option></option>');

        lstCategorias.forEach(function(categoria) {
            $('#categoria').append(
                $('<option></option>').val(categoria.id).text(categoria.descripcion)
            );
        });

        $('#categoria').select2({
            allowClear: true,
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        });

        const lastId    =   lstCategorias[lstCategorias.length -1].id;

        if (lastId) {
            $('#categoria').val(lastId).trigger('change'); 
        }
    }


    function pintarErroresValidacionCategoria(objErroresValidacion){
        for (let clave in objErroresValidacion) {
            const pError        =   document.querySelector(`.${clave}_error_categoria`);
            pError.textContent  =   objErroresValidacion[clave][0];
        }
    }

    async function getCategoriasActualizadas(){
        try {
            toastr.clear();
            const token                     =   document.querySelector('input[name="_token"]').value;
            const urlGetListCategorias      =   @json(route('registros.categoria.getListCategorias'));

            const response  =   await fetch(urlGetListCategorias, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                    });

            const   res     =   await response.json();
                
            if(res.success){
                
                toastr.clear();
                toastr.info(res.message,'CATEGORÍAS OBTENIDAS');
                return res.lstCategorias;
                
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR');
                return null;
            }
 
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN OBTENER CATEGORÍAS');
            return null;
        }
    }


</script>