<div class="modal fade" id="mdlEditCategoria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Categoría</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('registros.categorias.forms.form_edit_categoria')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary btnActualizarCategoria" type="submit" form="formActualizarCategoria">
            <i class="fa-solid fa-floppy-disk"></i> Actualizar
        </button>
        </div>
      </div>
    </div>
</div>


<script>
    let rowEditar   =   null;

    function eventsMdlEditCategoria(){
        document.querySelector('#formActualizarCategoria').addEventListener('submit',(e)=>{
            e.preventDefault();
            actualizarCategoria();
        })

        $('#mdlEditCategoria').on('hidden.bs.modal', function (e) {
            const   formActualizarCategoria    =   document.querySelector('#formActualizarCategoria');
            formActualizarCategoria.reset();
            limpiarErroresValidacion('msgError_edit');
        });
    }

    function openMdlEditCategoria(id){
        rowEditar  =   getRowById(dtCategorias,id);

        if(!rowEditar){
            toastr.error('NO SE ENCONTRÓ LA CATEGORÍA EN EL DATATABLE');
            return;
        }

        //======== SETTEANDO DATA ========
        document.querySelector('#descripcion_edit').value   =   rowEditar.nombre;

        $('#mdlEditCategoria').modal('show');
    }

    function actualizarCategoria(){
        
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ACTUALIZAR LA CATEGORÍA?",
        text: `Categoría: ${rowEditar.nombre}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ACTUALIZAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError_edit');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formActualizarCategoria   =   document.querySelector('#formActualizarCategoria');
            const formData                  =   new FormData(formActualizarCategoria);
            let urlUpdateCategoria          =   `{{ route('registros.categoria.update', ['id' => ':id']) }}`;
            urlUpdateCategoria              =   urlUpdateCategoria.replace(':id', rowEditar.id);

            Swal.fire({
                title: 'Cargando...',
                html: 'Actualizando categoría...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlUpdateCategoria, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                            'X-HTTP-Method-Override': 'PUT' 
                                        },
                                        body: formData
                                    });

                const   res =   await response.json();
                
                console.log(res);
                
                if(response.status === 422){
                    if('errors' in res){
                        pintarErroresValidacionEdit(res.errors);
                    }
                    Swal.close();
                    return;
                }
                
                if(res.success){
                    dtCategorias.draw();
                    $('#mdlEditCategoria').modal('hide');
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR CATEGORÍA');
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


    function pintarErroresValidacionEdit(objErroresValidacion){
        for (let clave in objErroresValidacion) {
            const pError        =   document.querySelector(`.${clave}_error`);
            pError.textContent  =   objErroresValidacion[clave][0];
        }
    }

</script>