<div class="modal fade" id="mdlAsistenciaSalida" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">ASISTENCIA SALIDA</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('jornales.registro_labor.forms.form_asistencia_salida')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary btnRegistrarMarca" type="submit" form="formAsistenciaSalida">
            <i class="fa-solid fa-floppy-disk"></i> Registrar
        </button>
        </div>
      </div>
    </div>
</div>


<script>

    const parametrosMdlSalida   =    {
                                        indice:null,
                                        colaborador_id:null
                                    }

    function eventsMdlAsistenciaSalida(){

        document.querySelector('#formAsistenciaSalida').addEventListener('submit',(e)=>{
            e.preventDefault();
            marcarSalida(parametrosMdlSalida.indice,parametrosMdlSalida.colaborador_id);
        })

        $('#mdlAsistenciaSalida').on('hidden.bs.modal', function (e) {
            const   formAsistenciaSalida                   =   document.querySelector('#formAsistenciaSalida');
            formAsistenciaSalida.reset();
            document.getElementById('img_vista_previa').src =   @json(asset('img/img_default.png'));
        });

      

        document.getElementById('tipo_asistencia_salida').addEventListener('change', function() {
            const horaEntradaContainer = document.getElementById('hora_salida_container');
            
            if (this.checked) {  // (AUTOMÁTICA)
                horaEntradaContainer.classList.remove('visible');
                horaEntradaContainer.classList.add('hidden');

                document.querySelector('#lbl_hora_salida').classList.remove('required_field');
                document.querySelector('#hora_salida').required = false;

            } else {  //(MANUAL)
                horaEntradaContainer.classList.remove('hidden');
                horaEntradaContainer.classList.add('visible');

                document.querySelector('#lbl_hora_salida').classList.add('required_field');
                document.querySelector('#hora_salida').required = true;

            }
        });


    }

    function openMdlAsistenciaSalida(indice,colaborador_id){
        parametrosMdlSalida.indice            =   indice;
        parametrosMdlSalida.colaborador_id    =   colaborador_id;
        $('#mdlAsistenciaSalida').modal('show');
    }

    function marcarSalida(rowId,colaborador_id){

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
        title: "DESEA MARCAR LA SALIDA?",
        text: `Usuario: ${fila[2]}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            toastr.clear();
            limpiarErroresValidacion('msgError');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formAsistenciaSalida     =   document.querySelector('#formAsistenciaSalida');
            const formData                  =   new FormData(formAsistenciaSalida);
            const urlMarcarSalida          =   @json(route('jornales.registro_labor.marcarSalida'));

            formData.append('registro_labor_id',@json($registro_labor_maestro->id));
            formData.append('colaborador_id',colaborador_id);

            Swal.fire({
                title: 'Cargando...',
                html: 'Marcando Salida...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlMarcarSalida, {
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
                    destruirDataTableDetalleAsistencia();
                    limpiarTabla('table_detalle_asistencia');
                    pintarTablaDetalleAsistencia(res.colaboradores);
                    iniciarDataTableDetalleAsistencia();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                    $('#mdlAsistenciaSalida').modal('hide');
                    
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
            if(pError){
                pError.textContent  =   objErroresValidacion[clave][0];
            }
            if(clave === 'registro_labor_id'){
                toastr.error(objErroresValidacion[clave][0],'ERROR VALIDACIÓN');
            }
            if(clave === 'colaborador_id'){
                toastr.error(objErroresValidacion[clave][0],'ERROR VALIDACIÓN');
            }
        }
    }



 


</script>