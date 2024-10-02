<div class="modal fade" tabindex="-1" id="mdlEditSubtarea">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">EDITAR SUBTAREA</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('plan_proyecto.tareas.forms.form_edit_subtarea')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button form="formEditSubtarea" type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </div>
</div>

<script>

  const subtarea_index_edit      =   {index:null};

  function eventsMdlEditSubtarea(){

    $('#mdlEditSubtarea').on('hidden.bs.modal', function (e) {
      limpiarMdlEditSubtarea();
    });

    document.addEventListener('click',(e)=>{
      if(e.target.classList.contains('btn-edit-subtarea')){
        const subtarea_index = e.target.getAttribute('data-id');

        if(!subtarea_index){
          toastr.error('ERROR AL OBTENER EL ÍNDICE DE LA SUBTAREA!!!');
          return;
        }
        
        //=========== OBTENIENDO SUBTAREA DEL LISTADO SUBTAREAS ======
        const subtarea = lstSubtareas[subtarea_index];

        //====== SETTEANDO EN EL MODAL =====
        setSubtareaEdit(subtarea,subtarea_index);

        openMdlEditSubtarea();
      }

      if(e.target.classList.contains('btn-delete-subtarea')){
        toastr.clear();
        const subtarea_index = e.target.getAttribute('data-id');

        if(!subtarea_index){
          toastr.error('ERROR AL OBTENER EL ÍNDICE DE LA SUBTAREA!!!');
          return;
        }
        
        //=========== ELIMINANDO SUBTAREA DEL LISTADO SUBTAREAS ======
        lstSubtareas.splice(subtarea_index,1);

        destruirDataTable(dtSubtareas);
        limpiarTabla('table_subtareas');
        pintarTableSubtareas(lstSubtareas);
        iniciarDataTableSubtareas();

        toastr.success('SUBTAREA ELIMINADA!!!');
        
      }
    })

    document.querySelector('#formEditSubtarea').addEventListener('submit',(e)=>{
      e.preventDefault();
      toastr.clear();

      limpiarMsgErrorsSubtareaEdit();

      //====== OBTENIENDO DATA =======  
      const subtarea    =   getDataFormEditSubtarea();

      //========= VALIDANDO ========
      const validacion  =   validarFormEditSubtarea(subtarea);
      
      if(validacion){

        console.log(subtarea);

        //======= ACTUALIZANDO SUBTAREA ====== 
        lstSubtareas[subtarea_index_edit.index] = {...subtarea};

        destruirDataTable(dtSubtareas);
        limpiarTabla('table_subtareas');
        pintarTableSubtareas(lstSubtareas);
        iniciarDataTableSubtareas();
        $('#mdlEditSubtarea').modal('hide');
        toastr.success('SUBTAREA ACTUALIZADA!!');

      }

    })

  }

  function openMdlEditSubtarea(){
    $('#mdlEditSubtarea').modal('show');
  }

  function setSubtareaEdit(subtarea,index){
    const subtarea_nombre_edit        = document.querySelector('#subtarea_nombre_edit');
    const subtarea_fecha_inicio_edit  = document.querySelector('#subtarea_fecha_inicio_edit');
    const subtarea_fecha_fin_edit     = document.querySelector('#subtarea_fecha_fin_edit');

    subtarea_nombre_edit.value        = subtarea.nombre;
    subtarea_fecha_inicio_edit.value  = subtarea.fecha_inicio;
    subtarea_fecha_fin_edit.value     = subtarea.fecha_fin;
    subtarea_observacion_edit.value   = subtarea.observacion;

    subtarea_index_edit.index = index;

  }

  function getDataFormEditSubtarea(){
    const subtarea      =   {nombre:null,fecha_inicio:null,fecha_fin:null,observacion:null};
    const nombre        =   document.querySelector('#subtarea_nombre_edit').value;
    const fecha_inicio  =   document.querySelector('#subtarea_fecha_inicio_edit').value;
    const fecha_fin     =   document.querySelector('#subtarea_fecha_fin_edit').value;
    const observacion   =   document.querySelector('#subtarea_observacion_edit').value;

    subtarea.nombre        =   nombre;
    subtarea.fecha_inicio  =   fecha_inicio;
    subtarea.fecha_fin     =   fecha_fin;
    subtarea.observacion   =   observacion;

    return subtarea;
  }

  function validarFormEditSubtarea(subtarea){
    let validacion  = true;

    if(!subtarea.nombre){ //==== NOMBRE REQUERIDO ====
      document.querySelector('.subtarea_nombre_edit_error').textContent  = 'El nombre es obligatorio';
      validacion  = false;
    }
    if(subtarea.nombre.trim().length === 0){  //===== LONGITUD NOMBRE ====
      document.querySelector('.subtarea_nombre_edit_error').textContent  = 'Debe ingresar un nombre válido';
      validacion  = false;
    }
    if(subtarea.nombre.trim().length > 150){ //===== LONGITUD NOMBRE ======
      document.querySelector('.subtarea_nombre_edit_error').textContent  = 'La longitud del nombre debe ser menor a 150 caracteres.';
      validacion  = false;
    }
    //======== NOMBRE REPETIDO =======
    const indiceSubtarea  = lstSubtareas.findIndex((ls,index)=>{
      return ls.nombre  == subtarea.nombre && index !=  subtarea_index_edit.index ;
    })
    if(indiceSubtarea !== -1){
      document.querySelector('.subtarea_nombre_edit_error').textContent  = 'El nombre de la subtarea está repetido!!!.';
      validacion  = false;
    }


    if(!subtarea.fecha_inicio){
      document.querySelector('.subtarea_fecha_inicio_edit_error').textContent  = 'La fecha de inicio es obligatoria';
      validacion  = false;
    }
    if(!subtarea.fecha_fin){
      document.querySelector('.subtarea_fecha_fin_edit_error').textContent  = 'La fecha de fin es obligatoria';
      validacion  = false;
    }

    if(subtarea.fecha_inicio  > subtarea.fecha_fin){
      document.querySelector('.subtarea_fecha_inicio_edit_error').textContent  = 'La fecha de inicio debe ser menor a la fecha de fin';
      document.querySelector('#subtarea_fecha_inicio_edit').focus();
      validacion  = false;
    }
    if(subtarea.fecha_fin  < subtarea.fecha_inicio){
      document.querySelector('.subtarea_fecha_fin_edit_error').textContent  = 'La fecha de fin debe ser mayor a la fecha de inicio';
      document.querySelector('#subtarea_fecha_fin_edit').focus();
      validacion  = false;
    }

    if(subtarea.observacion.trim().length > 300){
      document.querySelector('.subtarea_observacion_edit').textContent  = 'Longitud máxima permitida de 300 caracteres.';
      validacion  = false;
    }

    return validacion;
  }    

  function limpiarMsgErrorsSubtareaEdit(){
    const pErrors = document.querySelectorAll('.msgErrorSubtareaEdit');
    pErrors.forEach((element)=>{
      element.textContent = '';    
    })
  }

  function limpiarMdlEditSubtarea(){
    const subtarea_nombre_edit       =   document.querySelector('#subtarea_nombre_edit');
    const subtarea_fecha_inicio_edit =   document.querySelector('#subtarea_fecha_inicio_edit');
    const subtarea_fecha_fin_edit    =   document.querySelector('#subtarea_fecha_fin_edit');
    const subtarea_observacion_edit  =   document.querySelector('#subtarea_observacion_edit');

    subtarea_nombre_edit.value       = '';
    subtarea_fecha_inicio_edit.value = '';
    subtarea_fecha_fin_edit.value    = '';
    subtarea_observacion_edit.value  = '';
    
    subtarea_index_edit.index = null;
  }
</script>