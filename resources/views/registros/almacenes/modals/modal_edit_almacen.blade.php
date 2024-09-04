<div class="modal fade" id="mdlEditAlmacen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Almacén</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('registros.almacenes.forms.form_edit_almacen')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary btnActualizarAlmacen" type="submit" form="formActualizarAlmacen">
            <i class="fa-solid fa-floppy-disk"></i> Actualizar
        </button>
        </div>
      </div>
    </div>
</div>


<script>
    let rowEditar   =   null;

    function eventsMdlEditAlmacen(){
        document.querySelector('#formActualizarAlmacen').addEventListener('submit',(e)=>{
            e.preventDefault();
            actualizarAlmacen();
        })

        $('#mdlEditAlmacen').on('hidden.bs.modal', function (e) {
            const   formActualizarAlmacen    =   document.querySelector('#formActualizarAlmacen');
            formActualizarAlmacen.reset();
            limpiarErroresValidacion('msgError_edit');
        });
    }

    function openMdlEditAlmacen(id){
        rowEditar  =   getRowById(dtAlmacenes,id);

        if(!rowEditar){
            toastr.error('NO SE ENCONTRÓ EL ALMACÉN EN EL DATATABLE');
            return;
        }

        //======== SETTEANDO DATA ========
        document.querySelector('#descripcion_edit').value   =   rowEditar.nombre;

        $('#mdlEditAlmacen').modal('show');
    }

    function actualizarAlmacen(){
        
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ACTUALIZAR EL ALMACÉN?",
        text: `Almacén: ${rowEditar.nombre}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ACTUALIZAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError_edit');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formActualizarAlmacen     =   document.querySelector('#formActualizarAlmacen');
            const formData                  =   new FormData(formActualizarAlmacen);
            let urlUpdateAlmacen            =   `{{ route('registros.almacen.update', ['id' => ':id']) }}`;
            urlUpdateAlmacen                =   urlUpdateAlmacen.replace(':id', rowEditar.id);

            Swal.fire({
                title: 'Cargando...',
                html: 'Actualizando almacén...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlUpdateAlmacen, {
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
                    dtAlmacenes.draw();
                    $('#mdlEditAlmacen').modal('hide');
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR ALMACÉN');
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