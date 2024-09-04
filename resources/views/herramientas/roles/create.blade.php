@extends('layouts.layout')
@section('title-page')
    CREAR ROL
@endsection

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos del Rol<i class="fa-solid fa-user"></i></h6>
    </div>
    <div class="card-body">

        @include('herramientas.roles.forms.form_create_rol')

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
            <button class="btn btn-primary" type="submit" form="formRegistrarRol">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
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

        document.querySelector('#formRegistrarRol').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarRol();
        })

        document.addEventListener('click',(e)=>{

            //====== BTN VOLVER =======
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('herramientas.rol.index')}}';
                window.location.href    =   rutaIndex;
            }

        })

        document.addEventListener('change',(e)=>{
            if(e.target.classList.contains('chkPermiso')){
                
                const permiso_id    =   e.target.getAttribute('data-permiso-id');
                const marcado       =   e.target.checked;

                //=========== EN CASO SE MARCÓ EL CHECK =======
                if(marcado){
                    addPermiso(permiso_id);
                }else{
                    //======== ELIMINAR PERMISO ======
                    deletePermiso(permiso_id);
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

    //======== ELIMINAR PERMISO ======
    function deletePermiso(permiso_id){
        toastr.clear();
        //===== AGREGAR SI NO EXISTE EN EL LISTADO =======
        const indicePermiso =   lstPermisosAsignados.findIndex((permiso)=>{
            return permiso == permiso_id;
        })

        if(indicePermiso === -1){
            toastr.error('EL PERMISO NO ESTÁ AGREGADO EN LA LISTA');
        }else{
            lstPermisosAsignados.splice(indicePermiso,1);
        }
    }

    //======== AGREGAR PERMISO ======
    function addPermiso(permiso_id){
        toastr.clear();
        //===== AGREGAR SI NO EXISTE EN EL LISTADO =======
        const indicePermiso =   lstPermisosAsignados.findIndex((permiso)=>{
            return permiso == permiso_id;
        })

        if(indicePermiso === -1){
            lstPermisosAsignados.push(permiso_id);
        }else{
            toastr.error('EL PERMISO ESTÁ AGREGADO EN LA LISTA');
        }
    }

    //======= PINTAR TABLA ASIGNAR PERMISOS ======
    function pintarTableAsignarPermisos(){
        const permisos  =   @json($permisos);
        const tbody     =   document.querySelector('#table_asignar_permisos tbody');
        let filas       =   '';

        permisos.forEach((permiso)=>{
            let permiso_nombre  =   permiso.name;
            let permiso_menu    =   permiso_nombre.split('.')[0];
            let permiso_submenu =   permiso_nombre.split('.')[1];

            filas   +=  `<tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input chkPermiso" data-permiso-id="${permiso.id}" type="checkbox" value="" id="flexCheckDefault">
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

    function registrarRol(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR EL ROL?",
        text: "Se creará un nuevo rol!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError');
            const token                 =   document.querySelector('input[name="_token"]').value;
            const formRegistrarRol      =   document.querySelector('#formRegistrarRol');
            const formData              =   new FormData(formRegistrarRol);
            const urlRegistrarRol       =   @json(route('herramientas.rol.store'));
            formData.append('lstPermisosAsignados',JSON.stringify(lstPermisosAsignados));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nuevo rol...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarRol, {
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
                    const rol_index         =   @json(route('herramientas.rol.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   rol_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR ROL');
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

    

</script>


