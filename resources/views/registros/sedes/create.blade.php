@extends('layouts.layout')

@section('title-page')
    REGISTRAR SEDE
@endsection

@section('section-page')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Datos de la Sede <i class="fa-solid fa-building"></i></h6>
    </div>
    <div class="card-body">
        @include('registros.sedes.forms.form_create_sedes')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
        
        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formCreateSedes">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', () => {
        iniciarSelect2();
        events();
    });

    function events() {        
        document.querySelector('#formCreateSedes').addEventListener('submit', (e) => {
            e.preventDefault();
            registrarSede();
        });

        document.addEventListener('click', (e) => {
            if (e.target.closest('.btnVolver')) {
                const rutaIndex = '{{ route('registros.sedes.index') }}';
                window.location.href = rutaIndex;
            }
        });
    }

    function iniciarSelect2() {
        $('.select2_form').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });
    }

    function registrarSede() {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: "¿DESEA REGISTRAR LA SEDE?",
            text: "Se creará una nueva SEDE!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ, REGISTRAR!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                limpiarErroresValidacion('msgError');
                const token = document.querySelector('input[name="_token"]').value;
                const formCreateSedes = document.querySelector('#formCreateSedes');
                const formData = new FormData(formCreateSedes);
                const urlRegistrarSede = @json(route('registros.sedes.store'));

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Registrando nueva Sede...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(urlRegistrarSede, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    });

                    const res = await response.json();

                    if (response.status === 422) {
                        if ('errors' in res) {
                            pintarErroresValidacion(res.errors);
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {
                        const sede_index = @json(route('registros.sedes.index'));
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        window.location.href = sede_index;
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }

                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN REGISTRAR SEDE');
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

    function pintarErroresValidacion(objErroresValidacion) {
        for (let clave in objErroresValidacion) {
            const pError = document.querySelector(`.${clave}_error`);
            pError.textContent = objErroresValidacion[clave][0];
        }
    }
</script>
