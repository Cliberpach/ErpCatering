@extends('layouts.layout')
@section('title-page')
<img width="40" height="40" src="{{asset('img/icons/configuracion/configuracion1.png')}}" alt="coins"/>CONFIGURACIÓN
@endsection

@section('herramientas-collapsed', '')
@section('herramientas-expanded', 'true')
@section('herramientas-show', 'show')
@section('configuracion-active', 'active')


@section('section-page')
@include('reutilizables.lightbox.lightbox')
<div class="card-style settings-card-1 mb-30">
    <div class="card-body">
        @include('herramientas.configuracion.forms.form_configuracion')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        {{-- <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span> --}}
        {{-- <div style="display:flex;">
            <button class="btn btn-primary" type="submit" form="formActualizarEmpresa">
                <i class="fa-solid fa-floppy-disk"></i> ACTUALIZAR
            </button>
        </div> --}}
    </div>
</div>
@endsection

@if(Session::has('message_success'))
<script>
    var message = "{{ Session::get('message_success') }}";
    toastr.success(message, 'OPERACIÓN COMPLETADA');
</script>
@endif

<script>

    const parametros    =   {clickedGreenter:false};

    document.addEventListener('DOMContentLoaded',()=>{
        events();
    })

    function events(){
        

        //========= AMBIENTE GREENTER =======
        document.querySelector('.switch-input-ambiente-greenter').addEventListener('change', function (e) {
            
            e.target.checked = !e.target.checked;

            //======== PASO DE BETA A PRODUCCIÓN ========
            if (!e.target.checked) {
                actualizarConfigAmbienteGreenter('PRODUCCION',e.target);
            } else {
                //====== PASO DE PRODUCCIÓN A BETA ========
                actualizarConfigAmbienteGreenter('BETA',e.target);
            }

        });

       
       


     
    }

      //======= CONSULTAR DOCUMENTO IDENTIDAD =====
      async function consultarDocumento(tipo_documento,nro_documento){
        mostrarAnimacion1();
        try {
            const token                     =   document.querySelector('input[name="_token"]').value;
            const urlConsultarDocumento     =   `/empresa/consultarDocumento?tipo_documento=${encodeURIComponent(tipo_documento)}&nro_documento=${encodeURIComponent(nro_documento)}`;
            
            const response  =   await fetch(urlConsultarDocumento, {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': token 
                                    },
                                });

            const   res =   await response.json();

            if(res.success){
              
                if(tipo_documento == 2){
                    if(res.data.success){
                        setDatosRuc(res.data.data);
                        toastr.info(res.message);
                    }else{
                        toastr.error(res.data.message,'ERROR DEL API CONSULTA DE RUC');
                    }
                }

            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR AL CONSULTAR DOCUMENTO');
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN CONSULTAR DOCUMENTO');
        }finally{
            ocultarAnimacion1();
        }
    }

    function setDatosRuc(data){
        const nombre_o_razon_social     =   `${data.nombre_o_razon_social}`;
        const direccion_completa        =   data.direccion_completa;

        document.querySelector('#razon_social').value       =   nombre_o_razon_social;
        document.querySelector('#direccion').value          =   direccion_completa;
    }

    function goToCrearProducto(){
        window.location.href = @json(route('registros.producto.create'));
    }

    function actualizarConfigAmbienteGreenter(modo,inputChkGreenter){

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: `DESEA CAMBIAR AL AMBIENTE GREENTER: ${modo}`,
        text: "Se realizarán cambios!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cambiar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
           
            Swal.fire({
                title: 'Actualizando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();  
                }
            });

            try {

                limpiarErroresValidacion('msgError');
                toastr.clear();
                const id                        =   @json($configuraciones[0]->id);
                let urlUpdateConfigAmbienteGreenter =   `{{ route('herramientas.configuracion.ambiente_greenter', ['id' => ':id']) }}`;
                urlUpdateConfigAmbienteGreenter     =   urlUpdateConfigAmbienteGreenter.replace(':id', id);
                
                const token                     =   document.querySelector('input[name="_token"]').value;
                
                const formData                  =   new FormData();
                formData.append('modo',modo);

                // if(imgEmpresa.files.length > 0){
                //     formData.append('img_empresa', imgEmpresa.files[0]);
                // }

                // if(inputCertificado.files.length > 0){
                //     formData.append('certificado', inputCertificado.files[0]);
                // }

                const response  =   await fetch(urlUpdateConfigAmbienteGreenter, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                            'X-HTTP-Method-Override': 'PUT' 
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

                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    inputChkGreenter.checked    =   !inputChkGreenter.checked;

                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR AMBIENTE GREENTER');
            }finally{
                Swal.close();
            }

            
        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire({
            title: "Operación cancelada",
            text: "No se realizaron cambios",
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