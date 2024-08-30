<div class="modal fade" id="mdlEditMarca" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Marca</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('registros.marcas.forms.form_edit_marca')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary btnActualizarMarca" type="submit" form="formActualizarMarca">
            <i class="fa-solid fa-floppy-disk"></i> Actualizar
        </button>
        </div>
      </div>
    </div>
</div>


<script>
    let rowEditar   =   null;

    function eventsMdlEditMarca(){
        document.querySelector('#formActualizarMarca').addEventListener('submit',(e)=>{
            e.preventDefault();
            actualizarMarca();
        })
    }

    function openMdlEditMarca(id){
        rowEditar  =   getRowById(dtMarcas,id);

        if(!rowEditar){
            toastr.error('NO SE ENCONTRÓ LA MARCA EN EL DATATABLE');
            return;
        }

        //======== SETTEANDO DATA ========
        document.querySelector('#descripcion_edit').value   =   rowEditar.nombre;

        $('#mdlEditMarca').modal('show');
    }

    function actualizarMarca(){
        
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ACTUALIZAR LA MARCA?",
        text: `Marca: ${rowEditar.nombre}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ACTUALIZAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacionEdit();
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formActualizarMarca       =   document.querySelector('#formActualizarMarca');
            const formData                  =   new FormData(formActualizarMarca);
            let urlUpdateMarca              =   `{{ route('registros.marca.update', ['id' => ':id']) }}`;
            urlUpdateMarca                  =   urlUpdateMarca.replace(':id', rowEditar.id);

            Swal.fire({
                title: 'Cargando...',
                html: 'Actualizando marca...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlUpdateMarca, {
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
                    dtMarcas.draw();
                    $('#mdlEditMarca').modal('hide');
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR MARCA');
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

    function limpiarErroresValidacionEdit(){
        const lstEtiquetasErrors    =   document.querySelectorAll('.msgError_edit');
        lstEtiquetasErrors.forEach((etiqueta)=>{
            etiqueta.textContent    =   '';
        })
    }


</script>