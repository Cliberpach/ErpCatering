@extends('layouts.layout')
@section('title-page')
    ASISTENCIA DE COLABORADORES
@endsection

@section('section-page')

@include('reutilizables.lightbox.lightbox')
@include('jornales.registro_labor.modals.modal_asistencia_entrada')
@include('jornales.registro_labor.modals.modal_asistencia_salida')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Asistencia <i class="fa-solid fa-toolbox"></i></h6>
    </div>
    <div class="card-body">
        @include('jornales.registro_labor.forms.form_asistencias')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>
    let dtDetalleAsistencia =   null;
    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        pintarTablaDetalleAsistencia(@json($colaboradores));
        iniciarDataTableDetalleAsistencia();
        events();
    })

    function events(){
        eventsMdlAsistenciaEntrada();
        eventsMdlAsistenciaSalida();
        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('jornales.registro_labor.index')}}';
                window.location.href    =   rutaIndex;
            }

            //======== LIMPIAR IMAGEN =======
            if(e.target.classList.contains('btnSetImageDefault')){
                const inputImgPreview   =   document.querySelector('#img_vista_previa');
                inputImgPreview.src     =   @json(asset('img/img_default.png'));

                const inputCargarImg    =   document.querySelector('#img_asistencia_entrada');
                inputCargarImg.value    =   '';
            }
        })

    }

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        } );
        
    }

    function pintarTablaDetalleAsistencia(lstColaboradores){
        let filas                   =   ``;
        const tbody                 =   document.querySelector('#table_detalle_asistencia tbody');
        let acciones                =   ``;
        const supervisor_id         =   @json($registro_labor_maestro->supervisor_id);
        const asistencia_estado     =   @json($registro_labor_maestro->estado);
        const colaborador_actual_id =   @json($colaborador_actual_id);

      
        lstColaboradores.forEach((c, index) => {

            if((c.hora_entrada && c.hora_salida) || (supervisor_id != colaborador_actual_id) || (asistencia_estado === 'FINALIZADO') || (asistencia_estado === 'ANULADO') ){
                acciones    =   ``;
            }else{

                acciones    =   `<div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-sliders"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        ${!c.hora_entrada ? `
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlAsistenciaEntrada(${index}, ${c.colaborador_id})">
                                                    <i class="fa-solid fa-ticket"></i> Marcar entrada
                                                </a>
                                            </li>
                                        ` : ''}
                                        ${c.hora_entrada && !c.hora_salida ? `
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlAsistenciaSalida(${index}, ${c.colaborador_id})">
                                                    <i class="fa-solid fa-person-walking-dashed-line-arrow-right"></i> Marcar salida
                                                </a>
                                            </li>
                                        ` : ''}
                                    </ul>
                                </div>`;
            }

            //======= MANEJANDO IMAGEN =====
            let elementImg  =   ``;
            if(c.img_ruta){
                const ruta  =   @json(asset('')) + c.img_ruta;
                elementImg  =   `<img class="imgShowLightBox" style="height:50px;object-fit:contain;cursor:pointer;" src="${ruta}">`;
            }
        
            filas += `
                <tr>
                    <th>
                    </th>
                    <td>
                       ${acciones}  
                    </td>
                    <td>${c.colaborador_nombre}</td>
                    <td>
                        <div style="display:flex;justify-content:center;"> 
                            <p style="margin:0;">${c.colaborador_nro_documento}</p>
                        </div>
                    </td>
                    <td>${c.colaborador_tipo_documento}</td>
                    <td>${c.cargo_nombre}</td>
                    <td>
                        <div style="width:120px;">
                            ${c.hora_entrada || '<span class="badge text-bg-danger">NO REGISTRADO</span>'}
                        </div>
                    </td>
                    <td>
                        <div style="width:120px;">
                            ${c.hora_salida || '<span class="badge text-bg-danger">NO REGISTRADO</span>'}
                        </div>
                    </td>
                    <td>
                        ${c.estado}    
                    </td>
                    <td>
                        ${elementImg}    
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML =   filas;

    }

    function iniciarDataTableDetalleAsistencia(){
        dtDetalleAsistencia  =   new DataTable('#table_detalle_asistencia',{
            responsive: true,
            autoWidth: false,
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
            
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formMarcarAsistencia      =   document.querySelector('#formMarcarAsistencia');
            const formData                  =   new FormData();
            const urlMarcarSalida           =   @json(route('jornales.registro_labor.marcarSalida'));

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
                
                console.log(res);
                
                if(res.success){
                    destruirDataTableDetalleAsistencia();
                    limpiarTabla('table_detalle_asistencia');
                    pintarTablaDetalleAsistencia(res.colaboradores);
                    iniciarDataTableDetalleAsistencia();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    Swal.close();
                    
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN MARCAR SALIDA');
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

    function destruirDataTableDetalleAsistencia(){
        if(dtDetalleAsistencia){
            dtDetalleAsistencia.destroy();
            dtDetalleAsistencia =   null;
        }
    }

  
</script>


