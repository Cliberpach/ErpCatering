<div class="modal fade" id="mdlCreateTablaGeneralDetalle" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar {{ $titulo }}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('reutilizables.modals.tabla_general_detalle.forms.form_create_tabla_general_detalle')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary btnRegistrarMarca" type="submit" form="formRegistrarTablaGeneralDetalle">
            <i class="fa-solid fa-floppy-disk"></i> Registrar
        </button>
        </div>
      </div>
    </div>
</div>


<script>

    function eventsMdlCreateTablaGeneralDetalle(){
        document.querySelector('#formRegistrarTablaGeneralDetalle').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarTablaGeneralDetalle();
        })

        $('#mdlCreateTablaGeneralDetalle').on('hidden.bs.modal', function () {
           
            //======= RESETAER FORMULARIO ======
            const formRegistrarTablaGeneralDetalle    =   document.querySelector('#formRegistrarTablaGeneralDetalle');
            formRegistrarTablaGeneralDetalle.reset();

            limpiarErroresValidacion('msgError_tabla_general_detalle');

        });
    }

    function openMdlNuevaTablaGeneralDetalle(){
        $('#mdlCreateTablaGeneralDetalle').modal('show');
    }

    function registrarTablaGeneralDetalle(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: 'DESEA REALIZAR EL REGISTRO?',
        text: "Operación no reversible!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError_tabla_general_detalle');
            const token                             =   document.querySelector('input[name="_token"]').value;
            const formRegistrarTablaGeneralDetalle  =   document.querySelector('#formRegistrarTablaGeneralDetalle');
            const formData                          =   new FormData(formRegistrarTablaGeneralDetalle);
            const urlRegistrarTablaGeneralDetalle   =   @json(route('herramientas.tabla_general_detalle.store'));

            formData.append('tabla_general_id', @json($tabla_general_id));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando ...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarTablaGeneralDetalle, {
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
                        pintarErroresValidacionTablaGeneralDetalle(res.errors);
                    }
                    Swal.close();
                    return;
                }
                
                if(res.success){
                    //======== TRAER LISTADO DE MARCAS ACTUALIZADO =====
                    const tabla_general_id       =   @json($tabla_general_id);
                    const lstTablaGeneralDetalle =   await getTablaGeneralDetalle(tabla_general_id);
                    console.log(lstTablaGeneralDetalle);

                    //========= REPINTAR SELECT2 DE MARCAS ========
                    pintarSelect2TablaGeneralDetalle(lstTablaGeneralDetalle);
                    //console.log('lst marcas');
                    //console.log(lstTablaGeneralDetalle);
                    $('#mdlCreateTablaGeneralDetalle').modal('hide');
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR');
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

    function pintarSelect2TablaGeneralDetalle(lstDetalles){
        const select_2_id =   @json($select_2_id);
        $(`#${select_2_id}`).empty();

        $(`#${select_2_id}`).append('<option></option>');

        lstDetalles.forEach(function(detalle) {
            $(`#${select_2_id}`).append(
                $('<option></option>').val(detalle.id).text(detalle.descripcion)
            );
        });

        $(`#${select_2_id}`).select2({
            allowClear: true,
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        });

        const lastId    =   lstDetalles[lstDetalles.length -1].id;

        if (lastId) {
            $(`#${select_2_id}`).val(lastId).trigger('change'); 
        }
    }


    function pintarErroresValidacionTablaGeneralDetalle(objErroresValidacion){
        for (let clave in objErroresValidacion) {
            const pError        =   document.querySelector(`.${clave}_error_tabla_general_detalle`);
            pError.textContent  =   objErroresValidacion[clave][0];
        }
    }

    async function getTablaGeneralDetalle(tabla_general_id){
        try {
            toastr.clear();
            console.log('tabla_general_id',tabla_general_id);

            const token                             =   document.querySelector('input[name="_token"]').value;
            const urlBaseGetListTablaGeneralDetalle = "{{ route('registros.tabla_general_detalle.getListTablaGeneralDetalles', ['id' => 'ID_PLACEHOLDER']) }}";
            const urlGetListTablaGeneralDetalle     = urlBaseGetListTablaGeneralDetalle.replace('ID_PLACEHOLDER', tabla_general_id);

            const response  =   await fetch(urlGetListTablaGeneralDetalle, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                    });

            const   res     =   await response.json();
                
            if(res.success){
                
                toastr.clear();
                toastr.info(res.message,'ITEMS OBTENIDOS');
                return res.lstDetalles;
                
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR');
                return null;
            }
 
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN OBTENER ITEMS');
                return null;
            }
    }


</script>