@extends('layouts.layout')
@section('title-page')
    <i class="fa-solid fa-calendar-check" style="color:rgb(0, 68, 255);"></i> ASIGNAR HORARIO Y RÉGIMEN
@endsection

@section('section-page')

<div class="card-style settings-card-1 mb-30">
    <div class="title mb-30 d-flex justify-content-between align-items-center">
        <h6 style="font-weight:bold;">
            <i class="fa-solid fa-diagram-project" style="color:rgb(0, 68, 255);"></i> PROYECTO:
            <span>{{$proyecto->nombre}}</span>
        </h6>
    </div>
    <div class="card-body">
        @include('registros.proyectos.forms.form_asignar_horario_regimen')
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>

        <div style="display:flex;">
            <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                <i class="fa-solid fa-door-open"></i> VOLVER
            </button>
            <button class="btn btn-primary" type="submit" form="formAsignarHorarioRegimen">
                <i class="fa-solid fa-floppy-disk"></i> REGISTRAR
            </button>
        </div>
    </div>
</div>
<!-- end card -->
@endsection

<script>
    document.addEventListener('DOMContentLoaded', () => {
        iniciarSelect2();
        events();
    });

    function events() {
        document.querySelector('#formAsignarHorarioRegimen').addEventListener('submit', (e) => {
            e.preventDefault();
            asignarHorarioRegimen();
        });

        document.addEventListener('click', (e) => {
            if (e.target.closest('.btnVolver')) {
                const rutaIndex = '{{ route('registros.proyecto.index') }}';
                window.location.href = rutaIndex;
            }
        });
    }

    function iniciarSelect2() {
        $('.select2_form').select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
    }

    async function asignarHorarioRegimen() {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: "¿DESEA ASIGNAR EL HORARIO Y RÉGIMEN?",
            text: "Confirmar!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ, REGISTRAR!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                const token = document.querySelector('input[name="_token"]').value;
                const formData = new FormData(document.getElementById('formAsignarHorarioRegimen'));
                const urlAsignarHorarioRegimen = @json(route('registros.proyecto.asignarHorarioRegimenStore'));

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Asignando horario y régimen...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(urlAsignarHorarioRegimen, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token },
                        body: formData
                    });

                    const res = await response.json();

                    if (res.success) {
                        const proyectoIndex = @json(route('registros.proyecto.index'));
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        window.location.href = proyectoIndex;
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }
                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN');
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
</script>
