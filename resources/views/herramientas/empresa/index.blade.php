@extends('layouts.layout')
@section('title-page')
<i class="fa-solid fa-building" style="color: rgb(1, 70, 190);"></i> DATOS DE LA EMPRESA
@endsection

@section('herramientas-collapsed', '')
@section('herramientas-expanded', 'true')
@section('herramientas-show', 'show')
@section('empresa-active', 'active')


@section('section-page')
@include('reutilizables.lightbox.lightbox')
<div class="card-style settings-card-1 mb-30">
    <div class="card-body">
        @include('herramientas.empresa.forms.form_empresa')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        <div style="display:flex;">
            <button class="btn btn-primary" type="submit" form="formActualizarEmpresa">
                <i class="fa-solid fa-floppy-disk"></i> ACTUALIZAR
            </button>
        </div>
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

    document.addEventListener('DOMContentLoaded',()=>{
        events();
    })

    function events(){
        
        document.querySelector('#formActualizarEmpresa').addEventListener('submit',(e)=>{
            e.preventDefault();
            actualizarEmpresa();
        })

        document.addEventListener('click',(e)=>{
            //======== LIMPIAR IMAGEN =======
            if(e.target.classList.contains('btnSetImageDefault')){
                const inputImgPreview   =   document.querySelector('#img_vista_previa');
                inputImgPreview.src     =   @json(asset('img/img_default.png'));

                const inputCargarImg    =   document.querySelector('#img_empresa');
                inputCargarImg.value    =   '';
            }
        })

        document.querySelector('#telefono').addEventListener('input',(e)=>{
            const input = e.target;
            const maxLength = 20;
            
            // Expresión regular para validar números de teléfono internacionales
            const validPattern = /^\+?[0-9]*$/;

            // Reemplaza cualquier carácter que no sea un dígito o "+"
            let value = input.value.replace(/[^0-9+]/g, '');

            // Asegúrate de que el símbolo '+' esté al principio
            if (value.startsWith('+')) {
                value = '+' + value.slice(1).replace(/^\+/, '');
            } else {
                value = value.replace(/^\+/, '');
            }

            // Limita el valor a 20 caracteres
            if (value.length > maxLength) {
                value = value.slice(0, maxLength);
            }

            // Actualiza el valor del input
            input.value = value;
        })

        document.querySelector('#img_empresa').addEventListener('change', function(event) {
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

        //======= CONSULTAR API DOCUMENTO DNI ========
        document.querySelector('#btn_consultar_documento').addEventListener('click',()=>{
            const nro_documento     =   document.querySelector('#ruc').value;
            const tipo_documento    =   2;
            toastr.clear();

            if(tipo_documento != 2){
                toastr.error('SOLO SE PUEDE CONSULTAR TIPO DE DOCUMENTO RUC');
                return;
            }

            if(!nro_documento){
                toastr.error('DEBE INGRESAR UN NRO DE DOCUMENTO VÁLIDO');
                return;
            }

            if(tipo_documento == 2){
                if(nro_documento.length != 11){
                    toastr.error('NRO DE RUC DEBE CONTAR CON 11 DÍGITOS');
                    return;
                }
            }

            consultarDocumento(tipo_documento,nro_documento);

        })
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

    function actualizarEmpresa(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "Desea actualizar los datos de la empresa?",
        text: "Se realizarán cambios!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, actualizar!",
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
                const imgEmpresa                =   document.getElementById('img_empresa');
                const id                        =   @json($empresa->id);
                let urlUpdateEmpresa            =   `{{ route('herramientas.empresa.update', ['id' => ':id']) }}`;
                urlUpdateEmpresa                =   urlUpdateEmpresa.replace(':id', id);
                const token                     =   document.querySelector('input[name="_token"]').value;
                const formActualizarEmpresa     =   document.querySelector('#formActualizarEmpresa');
                const formData                  =   new FormData(formActualizarEmpresa);

                if(imgEmpresa.files.length > 0){
                    formData.append('img_empresa', imgEmpresa.files[0]);
                }

                const response  =   await fetch(urlUpdateEmpresa, {
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

                    //========== GENERANDO VALOR DE TIEMPO ÚNICO PARA LA CACHÉ =======
                    const timestamp     =   new Date().getTime();
                    const imgUrl        =   `${@json(asset(''))}${res.empresa.img_ruta}?t=${timestamp}`;

                    //========= ACTUALIZAR LA IMAGEN SIN PROBLEMAS DE CACHÉ ========
                    const imgElement    =   document.querySelector('#img_nav_empresa');
                    imgElement.src      =   imgUrl;

                    //======== ACTUALIZAR NOMBRE DE LA EMPRESA ========
                    document.querySelector('#nombre_nav_empresa').textContent   =   res.empresa.razon_social;
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    //window.location.reload();
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR PRODUCTO');
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


    function eliminarProducto(id){
        toastr.clear();
        let row             =   getRowById(dtProductos,id);
        let message         =   '';
        let tipo_documento  =   '';

        message =   `Desea eliminar el producto: ${row.nombre}`;

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: message,
        text: "Operación no reversible!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            
            Swal.fire({
                title: 'Cargando...',
                html: 'Eliminando producto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteProducto    =   `{{ route('registros.producto.destroy', ['id' => ':id']) }}`;
                urlDeleteProducto        =   urlDeleteProducto.replace(':id', id);
                const token              =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteProducto, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtProductos.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR PRODUCTO');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR PRODUCTO');
            }finally{
                Swal.close();
            }

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire({
            title: "Operación cancelada",
            text: "No se realizaron acciones",
            icon: "error"
            });
        }
        });
    }


</script>