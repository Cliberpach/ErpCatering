@extends('layouts.layout')
@section('title-page')
    REGISTRAR PROYECTO
@endsection

@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('proyectos-active', 'active')

@section('section-page')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos del Proyecto<i class="fa-solid fa-toolbox"></i></h6>
    </div>
    <div class="card-body">
        @include('registros.proyectos.forms.form_create_proyecto')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formRegistrarProyecto">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection


<script>
    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        events();
    })

    function events(){
        document.querySelector('#formRegistrarProyecto').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarProyecto();
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('registros.proyecto.index')}}';
                window.location.href    =   rutaIndex;
            }
        })

        //=========== AL ESCRIBIR EN INPUT COSTO O AVANCE COSTO =======
        document.addEventListener('input',(e)=>{
            if(e.target.classList.contains('costo') || e.target.classList.contains('avance_costo')){
                calcularDiferencia();
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

    function calcularDiferencia(){
        let costo         =   document.querySelector('#costo').value ? document.querySelector('#costo').value : 0;
        let avance_costo  =   document.querySelector('#avance_costo').value ? document.querySelector('#avance_costo').value : 0;

        costo           =   parseFloat(costo);
        costo           =   Math.trunc(costo * 100) / 100;
        avance_costo    =   parseFloat(avance_costo);
        avance_costo    =   Math.trunc(avance_costo * 100) / 100;


        if (isNaN(costo) && isNaN(avance_costo)) {
            toastr.clear();
            toastr.error('EL COSTO Y AVANCE COSTO NO SON VALORES NUMÉRICOS');
            document.querySelector('#diferencia').value =   0;
            return;
        }

     
        let diferencia  =   costo - avance_costo;
        diferencia      =   diferencia.toFixed(2);

        document.querySelector('#diferencia').value =   diferencia;
    }

    function registrarProyecto(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR EL PROYECTO?",
        text: "Se creará un nuevo proyecto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            limpiarErroresValidacion('msgError');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formRegistrarProyecto     =   document.querySelector('#formRegistrarProyecto');
            const formData                  =   new FormData(formRegistrarProyecto);
            const urlRegistrarProyecto      =   @json(route('registros.proyecto.store'));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nuevo proyecto...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarProyecto, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
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
                    const proyecto_index     =   @json(route('registros.proyecto.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   proyecto_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR PROYECTO');
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

    function cambiarDepartamento(selectDepartamento){

        let departamento_id   =   selectDepartamento.value;
        const lstProvincias     =   @json($provincias);
        const lstDistritos      =   @json($distritos);

        let lstProvinciasFiltradas      =   [];
        
        if(departamento_id){
            departamento_id = String(departamento_id).padStart(2, '0');

            lstProvinciasFiltradas      =   lstProvincias.filter((provincia)=>{
                return  provincia.departamento_id == departamento_id;
            })   

            $('#provincia').empty().trigger('change');

            lstProvinciasFiltradas.forEach((provincia)=>{
                $('#provincia').append(new Option(provincia.nombre, provincia.id, false, false));
            })

            $('#provincia').select2({
                theme: "bootstrap-5",
                placeholder: 'Seleccione una provincia',
                width: '100%'
            });

            $('#provincia').trigger('change');
        }

        // console.log(departamento_id);
        // console.log(lstProvincias)
        // console.log(lstDistritos);

        // console.log('PROVINCIAS FILTRADAS');
        // console.log(lstProvinciasFiltradas)
    }

    function cambiarProvincia(selectProvincia){

        let provincia_id        =   selectProvincia.value;
        const lstDistritos      =   @json($distritos);

        console.log('DISTRITOS');
        console.log(lstDistritos);


        let lstDistritosFiltrados      =   [];

        if(provincia_id){
            provincia_id = String(provincia_id).padStart(4, '0');

            console.log('PROVINCIA ID');
            console.log(provincia_id);

            lstDistritosFiltrados      =   lstDistritos.filter((distrito)=>{
                return  distrito.provincia_id == provincia_id;
            })   

            $('#distrito').empty().trigger('change');

            lstDistritosFiltrados.forEach((distrito)=>{
                $('#distrito').append(new Option(distrito.nombre, distrito.id, false, false));
            })

            $('#distrito').select2({
                theme: "bootstrap-5",
                placeholder: 'Seleccione un distrito',
                width: '100%'
            });
        }


        console.log('DISTRITOS FILTRADOS');
        console.log(lstDistritosFiltrados)
    }

</script>


