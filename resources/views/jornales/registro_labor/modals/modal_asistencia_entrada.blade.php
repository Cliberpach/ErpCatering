<div class="modal fade" id="mdlAsistenciaEntrada" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">ASISTENCIA ENTRADA</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('jornales.registro_labor.forms.form_asistencia_entrada')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary btnRegistrarMarca" type="submit" form="formAsistenciaEntrada">
            <i class="fa-solid fa-floppy-disk"></i> Registrar
        </button>
        </div>
      </div>
    </div>
</div>


<script>

    const parametrosMdl   =     {
                                    indice:null,
                                    colaborador_id:null
                                }

    function eventsMdlAsistenciaEntrada(){

        document.querySelector('#formAsistenciaEntrada').addEventListener('submit',(e)=>{
            e.preventDefault();
            marcarEntrada(parametrosMdl.indice,parametrosMdl.colaborador_id);
        })

        $('#mdlAsistenciaEntrada').on('hidden.bs.modal', function (e) {
            const   formAsistenciaEntrada                   =   document.querySelector('#formAsistenciaEntrada');
            formAsistenciaEntrada.reset();
            document.getElementById('img_vista_previa').src =   @json(asset('img/img_default.png'));
        });

        document.querySelector('#img_asistencia_entrada').addEventListener('change', function(event) {
            const file      =   event.target.files[0];
            const reader    =   new FileReader();

            if (file) {

                reader.onload = function(e) {
                    document.getElementById('img_vista_previa').src = e.target.result;
                };

                reader.readAsDataURL(file);
            } else {
                document.getElementById('img_vista_previa').src = @json(asset('img/img_default.png'));
            }
            
        });

    }

    function openMdlAsistenciaEntrada(indice,colaborador_id){
        parametrosMdl.indice            =   indice;
        parametrosMdl.colaborador_id    =   colaborador_id;
        $('#mdlAsistenciaEntrada').modal('show');
    }

    function marcarEntrada(rowId,colaborador_id){

        const fila  =   dtDetalleAsistencia.row(rowId).data();
        if(fila.length === 0){
            toastr.error('NO SE ENCONTRÓ LA FILA EN EL DATATABLE');
            return;
        }
       
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA MARCAR LA ASISTENCIA DE INGRESO?",
        text: `Usuario: ${fila[2]}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formAsistenciaEntrada     =   document.querySelector('#formAsistenciaEntrada');
            const formData                  =   new FormData(formAsistenciaEntrada);
            const urlMarcarEntrada          =   @json(route('jornales.registro_labor.marcarEntrada'));

            formData.append('registro_labor_id',@json($registro_labor_maestro->id));
            formData.append('colaborador_id',colaborador_id);

            Swal.fire({
                title: 'Cargando...',
                html: 'Marcando Ingreso...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlMarcarEntrada, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                        body: formData
                                    });

                const   res =   await response.json();
                
                console.log(res);
                
                if(res.success){
                    destruirDataTableDetalleAsistencia();
                    limpiarTabla('table_detalle_asistencia');
                    pintarTablaDetalleAsistencia(res.colaboradores);
                    iniciarDataTableDetalleAsistencia();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                    $('#mdlAsistenciaEntrada').modal('hide');
                    
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN MARCAR INGRESO');
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

 


</script>