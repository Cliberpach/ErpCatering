<!-- Modal Edit Plato -->
<div class="modal fade" id="mdlEditPlato" tabindex="-1" aria-labelledby="modalEditarPlatoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarPlatoLabel">Editar Plato</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           @include('registros.platos.forms.form_edit_plato')

           <div class="modal-footer">
                <small class="text-muted me-auto">
                    <span class="text-danger">*</span> Campos obligatorios.
                </small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary btnActualizarPlato" type="submit" form="formEditarPlato">
                    <i class="fa-solid fa-floppy-disk"></i> Actualizar
            </div>
        </div> 
    </div>  
</div>

<script>
    let rowEditar   =   null;

    function eventsMdlEditPlato(){
        document.querySelector('#formEditarPlato').addEventListener('submit',(e)=>{
            e.preventDefault();
            actualizarPlato();
        })

        $('#mdlEditPlato').on('hidden.bs.modal', function (e) {
            const   formEditarPlato    =   document.querySelector('#formEditarPlato');
            formEditarPlato.reset();
            limpiarErroresValidacion('msgError_edit');
        });
    }

    function openMdlEditPlato(id){
        rowEditar  =   getRowById(dtPlatos,id);

        if(!rowEditar){
            toastr.error('NO SE ENCONTRÓ EL PLATO EN EL DATATABLE');
            return;
        }

        //======== SETTEANDO DATA ========
        document.querySelector('#editarNombre').value           =     rowEditar.nombre;
        document.querySelector('#editarCalorias').value         =     rowEditar.calorias;
        document.querySelector('#editarProteinas').value        =     rowEditar.proteinas;
        document.querySelector('#editarCarbohidratos').value    =     rowEditar.carbohidratos;
        document.querySelector('#editarGrasas').value           =     rowEditar.grasas;
        document.querySelector('#editarPeso').value             =     rowEditar.peso;
        document.querySelector('#editarCosto').value            =     rowEditar.costo;


        $('#mdlEditPlato').modal('show');
    }

    function actualizarPlato(){
        
        Swal.fire({
        title: "DESEA ACTUALIZAR EL PLATO?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ACTUALIZAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError_edit');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formEditarPlato           =   document.querySelector('#formEditarPlato');
            const formData                  =   new FormData(formEditarPlato);
            let urlUpdatePlato = `{{ route('registros.platos.update', ['id' => ':id']) }}`;
            urlUpdatePlato = urlUpdatePlato.replace(':id', rowEditar.id);

            Swal.fire({
                title: 'Cargando...',
                html: 'Actualizando Plato...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlUpdatePlato, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                            'X-HTTP-Method-Override': 'PUT',
                                            'Accept': 'application/json',
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
                    dtPlatos.draw();
                    $('#mdlEditPlato').modal('hide');
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR PLATO');
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

    function limpiarErroresValidacion() {
        document.querySelectorAll('.msgError').forEach((el) => {
            el.textContent = '';
        });
    }

</script>