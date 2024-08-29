@extends('layouts.layout')
@section('title-page')
    EDITAR ROL
@endsection

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos del Rol<i class="fa-solid fa-user"></i></h6>
    </div>
    <div class="card-body">

       @include('herramientas.roles.forms.form_edit_rol')

        <div class="row">
            <div class="col-12 mt-3 mb-3">
                <div class="card">
                    <div class="card-header" style="background-color: rgb(0, 102, 255);font-weight:bold;color:white;">
                    ASIGNAR PERMISOS
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @include('herramientas.roles.tables.table_asignar_permisos')
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formActualizarRol">
                <i class="fa-solid fa-floppy-disk"></i> ACTUALIZAR
            </button>
        </div>
        
    </div>
</div>
<!-- end card -->
@endsection


<script>
    
    let dtAsignarPermisos           =   null;
    const lstPermisosAsignados      =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        pintarTableAsignarPermisos();
        iniciarDataTableAsignarPermisos();
        events();
    })

    function events(){
        document.querySelector('#formActualizarRol').addEventListener('submit',(e)=>{
            e.preventDefault();
            
            actualizarRol();
        })

        //======== BTN VER CONTRASEÑA =========
        document.addEventListener('click',(e)=>{

            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('herramientas.usuario.index')}}';
                window.location.href    =   rutaIndex;
            }

            if (e.target.closest('.btn_ver_password')) {
               
                const btnVerPassword   =   e.target.closest('.btn_ver_password');
                btnVerPassword.classList.toggle('password_oculto');

                if(btnVerPassword.classList.contains('password_oculto')){
                    //======== OCULTAR PASSWORD =======
                    document.querySelector('#password').type    =   'password';
                     //===== CAMBIANDO ICONO =====
                     const icon  =   btnVerPassword.children[0];

                    icon.classList.add('hide-transition');
                    setTimeout(() => {
                        icon.className = 'fa-solid fa-eye-slash show-transition';
                        icon.classList.remove('hide-transition');
                    }, 300);
                }else{
                    //====== MOSTRAR PASSWORD =======
                    document.querySelector('#password').type    =   'text';
                    //===== CAMBIANDO ICONO =====
                    const icon  =   btnVerPassword.children[0];
                    icon.classList.add('hide-transition');
                    setTimeout(() => {
                        icon.className = 'fa-solid fa-eye show-transition';
                    }, 300); 
                }
            }

            if (e.target.closest('.btn_ver_repetir_password')) {
                
                const btnVerRepetirPassword   =   e.target.closest('.btn_ver_repetir_password');
                btnVerRepetirPassword.classList.toggle('password_oculto');

                if(btnVerRepetirPassword.classList.contains('password_oculto')){
                    //======== OCULTAR PASSWORD =======
                    document.querySelector('#repetir_password').type    =   'password';
                    //===== CAMBIANDO ICONO =====
                    const icon  =   btnVerRepetirPassword.children[0];

                    icon.classList.add('hide-transition');
                    setTimeout(() => {
                        icon.className = 'fa-solid fa-eye-slash show-transition';
                        icon.classList.remove('hide-transition');
                    }, 300);

                }else{
                    //====== MOSTRAR PASSWORD =======
                    document.querySelector('#repetir_password').type    =   'text';
                    //===== CAMBIANDO ICONO =====
                    const icon  =   btnVerRepetirPassword.children[0];
                    icon.classList.add('hide-transition');
                    setTimeout(() => {
                        icon.className = 'fa-solid fa-eye show-transition';
                    }, 300); 
                }
            }

        })
    }

    function iniciarSelect2(){
        $('#colaborador').select2({
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        });
    }

    function iniciarDataTableAsignarPermisos(){
        dtAsignarPermisos  =   new DataTable('#table_asignar_permisos',{
            language: {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "emptyTable": "No hay datos disponibles en la tabla",
                "aria": {
                    "sortAscending": ": activar para ordenar la columna de manera ascendente",
                    "sortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }

    function pintarTableAsignarPermisos(){

        const permisos_asignados    =   @json($permisos_asignados);
        const permisos              =   @json($permisos);
        const tbody                 =   document.querySelector('#table_asignar_permisos tbody');
        let filas               =   '';

        permisos.forEach((permiso)=>{
            let permiso_nombre  =   permiso.name;
            let permiso_menu    =   permiso_nombre.split('.')[0];
            let permiso_submenu =   permiso_nombre.split('.')[1];
            let marcado         =   '';

            //======== REVIZANDO SI EL PERMISO ESTÁ ASIGNADO =======
            let indicePermisoAsignado   =   permisos_asignados.findIndex((permiso_asignado)=>{
                return permiso_asignado.permission_id == permiso.id;
            })   

            if(indicePermisoAsignado !== -1){
                marcado =   'checked';
            }

            filas   +=  `<tr>
                            <th>
                                <div class="form-check">
                                    <input ${marcado} class="form-check-input chkPermiso" data-permiso-id="${permiso.id}" type="checkbox" value="" id="flexCheckDefault">
                                </div>     
                            </th>
                            <td style="text-align:start;">
                                ${permiso.id}    
                            </td>
                            <td>
                                ${permiso_menu}
                            </td>
                            <td>
                                ${permiso_submenu}
                            </td>
                        </tr>`;
        })

        tbody.innerHTML =   filas;

    }

    function actualizarRol(){

        const rol   =   @json($rol);

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA ACTUALIZAR EL USUARIO?",
        text: `ROL: ${rol.name}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, ACTUALIZAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion();
            
            Swal.fire({
                title: 'Cargando...',
                html: 'Actualizando usuario...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {

                const formActualizarRol =   document.querySelector('#formActualizarRol');
                const formData              =   new FormData(formActualizarRol);
                const token                 =   document.querySelector('input[name="_token"]').value;
                const id                    =   @json($rol->id);
                let urlUpdateUsuario        =   `{{ route('herramientas.usuario.update', ['id' => ':id']) }}`;
                urlUpdateUsuario            =   urlUpdateUsuario.replace(':id', id);

                const response  =   await fetch(urlUpdateUsuario, {
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
                        pintarErroresValidacion(res.errors);
                    }
                    Swal.close();
                    return;
                }
                

                if(res.success){
                    const usuario_index     =   @json(route('herramientas.usuario.index'));
                    toastr.success(response.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   usuario_index;
                }else{
                    toastr.error(response.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR USUARIO');
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
            const pError    =   document.querySelector(`.${clave}_error`);
            pError.textContent  =   objErroresValidacion[clave][0];
        }
    }

    function limpiarErroresValidacion(){
        const lstEtiquetasErrors    =   document.querySelectorAll('.msgError');
        lstEtiquetasErrors.forEach((etiqueta)=>{
            etiqueta.textContent    =   '';
        })
    }

</script>


