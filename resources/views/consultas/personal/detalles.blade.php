@extends('layouts.layout')
@section('title-page')
    CONSULTA DE PERSONAL
@endsection

@section('consultas-collapsed', '')
@section('consultas-expanded', 'true')
@section('consultas-show', 'show')
@section('personal-active', 'active')


@section('section-page')

<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Días Trabajo <i class="fa-solid fa-clock"></i></h6>
    </div>

    <div class="row mb-3">
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
            <label for="fecha_inicio" style="font-weight: bold;">FECHA INICIO</label>
            <input value="<?php echo date('Y-m-d'); ?>" type="date" id="fecha_inicio" class="form-control" onchange="cambioFechaInicio();">
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
            <label for="fecha_fin" style="font-weight: bold;">FECHA FIN</label>
            <input value="<?php echo date('Y-m-d'); ?>" type="date" id="fecha_fin" class="form-control" onchange="cambioFechaFin();">
        </div>
    </div>
    <div class="row mb-3 justify-content-end">
        

    </div>
    <div class="table-responsive">
        @include('consultas.personal.tables.detalles_table')
    </div>

    @include('consultas.personal.modals.asignar_motivo')
    @include('consultas.personal.modals.asignar_horas_extra')
</div>


<script>
    document.addEventListener('DOMContentLoaded', () => {
    iniciarDataTableConsultaPersonalDetalle();
    iniciarSelect2();
});

function iniciarSelect2() {
    $('.select2_form').select2({
        theme: "bootstrap-5",
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
    });
}

function iniciarDataTableConsultaPersonalDetalle() {


    dtConsultaPersonal = new DataTable('#table_detalles_personal', {
    serverSide: true,
    processing: true,
    ajax: {
        url: '{{ route("consultas.personal.getDetalles", $colaborador_id) }}',
        type: 'GET',
        data: function(d) {
            d.fecha_inicio = $('#fecha_inicio').val();
            d.fecha_fin = $('#fecha_fin').val();
        }
    },
    columns: [
        { data: 'fecha_asistencia', name: 'fecha_asistencia' }, // Fecha
        { data: 'motivo_permiso', name: 'motivo_permiso' }, // Permiso (Motivo)
        { data: 'turno', name: 'turno' }, // Turno
        { data: 'hora_entrada', name: 'hora_entrada' }, // Hora Entrada
        { data: 'hora_entrada_break', name: 'hora_entrada_break' }, // Hora Entrada Break
        { data: 'hora_salida_break', name: 'hora_salida_break' }, // Hora Salida Break
        { data: 'hora_salida', name: 'hora_salida' }, // Hora Salida
        { data: 'retraso', name: 'retraso' }, // Retraso
        { data: 'adelanto', name: 'adelanto' }, // Adelanto
        { data: 'horas_extra', name: 'horas_extra' }, // Horas Extra
        { data: 'horas_no_trabajadas', name: 'horas_no_trabajadas' }, // Horas No Trabajadas
        { data: 'tiempo_trabajado', name: 'tiempo_trabajado' }, // Horas Trabajadas
        {
            data: null, 
            render: function(data, type, row) {
                return `
                    <div class="btn-group dropstart">
                        <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-grip"></i>
                        </button>
                        <ul class="dropdown-menu" style="max-height: 150px; overflow-y: auto;">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="abrirModalMotivo(${data.id})">
                                    <i class="fa-solid fa-pen-to-square"></i> Asignar Permiso
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="abrirModalHorasExtra(${data.id})">
                                    <i class="fa-solid fa-pen-to-square"></i> Asignar Horas Extra
                                </a>
                            </li>
                        </ul>
                    </div>
                `;
    },
    name: 'actions', 
    orderable: false, 
    searchable: false 
}
    ],
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




    function cambioFechaFin(){
        const fecha_inicio  =   document.querySelector('#fecha_inicio');
        const fecha_fin     =   document.querySelector('#fecha_fin');
        
        if(fecha_fin.value < fecha_inicio.value && fecha_inicio.value && fecha_fin.value){
            toastr.error('LA FECHA DE FIN DEBE SER MAYOR A LA FECHA DE INICIO!!');
            fecha_fin.value =   '';
            fecha_fin.focus();
            return;
        }
        dtConsultaPersonal.ajax.reload();
    }

    function cambioFechaInicio() {
        const fecha_inicio  =   document.querySelector('#fecha_inicio');
        const fecha_fin     =   document.querySelector('#fecha_fin');
        
        if(fecha_inicio.value > fecha_fin.value && fecha_inicio.value && fecha_fin.value){
            toastr.error('LA FECHA DE INICIO DEBE SER MENOR A LA FECHA DE FIN!!');
            fecha_inicio.value =   '';
            fecha_inicio.focus();
            return;
        }
        dtConsultaPersonal.ajax.reload();
    }


    function abrirModalMotivo(id) {
    // Establecer el ID de la fila de asistencia detalle
    $('#asistenciaDetalleId').val(id);

    // Primero, mostramos el modal para ingresar la contraseña
    const modalPassword = new bootstrap.Modal(document.getElementById('modalPassword'));
    modalPassword.show();

    // Lógica para verificar la contraseña
    document.querySelector('#verifyPasswordBtn').addEventListener('click', function () {
        const password = document.getElementById('password').value;

        // Enviar la contraseña al backend para validarla
        fetch('/consultas_personal/verificar-contraseña', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json', 
            },
            body: JSON.stringify({ password: password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Contraseña correcta, cerramos el modal de contraseña
                modalPassword.hide();

                // Ahora obtenemos los motivos de descanso y cargamos el select2
                $.ajax({
                    url: '/consultas_personal/get-motivos-descanso', // Ruta donde obtenemos los motivos
                    method: 'GET',
                    success: function(data) {
                        let options = '<option value="">Seleccionar motivo</option>';
                        data.motivos.forEach(function(motivo) {
                            options += `<option value="${motivo.id}">${motivo.descripcion}</option>`;
                        });
                        $('#motivoSelect').html(options); // Cargar los motivos en el select

                        // Abrir el modal de asignar permiso
                        $('#modalAsignarPermiso').modal('show');
                    }
                });
            } else {
                // Si la contraseña es incorrecta, mostrar mensaje de error
                document.getElementById('passwordError').textContent = "Contraseña incorrecta. Inténtalo nuevamente.";
                document.getElementById('password').classList.add('is-invalid');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error al verificar la contraseña");
        });
    });
}


// Seleccionamos el formulario y el botón de guardar
const formMotivoPermiso = document.querySelector('#formMotivoPermiso');
const botonGuardarMotivoPermiso = formMotivoPermiso.querySelector('button[type="submit"]');

botonGuardarMotivoPermiso.addEventListener('click', (e) => {
    e.preventDefault(); // Evita el envío inmediato del formulario

    // Abre el modal de confirmación
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Se asignará el motivo de descanso a este detalle de asistencia.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, asignar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar los datos del formulario con fetch
            const formData = new FormData(formMotivoPermiso);
        
            fetch('/consultas_personal/guardar-motivo-permiso', {
                method: 'POST',
                headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json', 
                    },
                body: formData
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Guardado!',
                        text: data.success,
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    // Cerrar el modal
                    const modalCrearMotivo = bootstrap.Modal.getInstance(document.getElementById('modalAsignarPermiso'));
                    modalCrearMotivo.hide();

                    // Recargar la tabla
                    $('#table_detalles_personal').DataTable().ajax.reload();
                } else if (data.errors) {
                    // Manejar errores de validación
                    for (const field in data.errors) {
                        const input = formMotivoPermiso.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            input.nextElementSibling.textContent = data.errors[field][0];
                        }
                    }
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al asignar el motivo de permiso.',
                });
            });
        }
    });
});



// Función para abrir el modal de horas extra con validación de contraseña
function abrirModalHorasExtra(id) {
    // Mostrar el modal de contraseña
    const modalPassword = new bootstrap.Modal(document.getElementById('modalPassword'));
    modalPassword.show();

    // Lógica para verificar la contraseña
    document.querySelector('#verifyPasswordBtn').addEventListener('click', function () {
        const password = document.getElementById('password').value;

        // Enviar la contraseña al backend para validarla
        fetch('/consultas_personal/verificar-contraseña', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json', 
            },
            body: JSON.stringify({ password: password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Contraseña correcta, cerramos el modal de contraseña
                modalPassword.hide();

                // Ahora obtenemos los datos de horas extra
                obtenerHorasExtra(id);
            } else {
                // Si la contraseña es incorrecta, mostrar mensaje de error
                document.getElementById('passwordError').textContent = "Contraseña incorrecta. Inténtalo nuevamente.";
                document.getElementById('password').classList.add('is-invalid');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error al verificar la contraseña");
        });
    });
}

// Función para obtener los datos de horas extra y abrir el modal de horas extra
function obtenerHorasExtra(id) {
    // Enviar solicitud al servidor para obtener las horas extra del detalle de asistencia
    fetch(`/consultas_personal/get-horas-extra/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Prellenar el formulario con los datos de horas extra
                document.getElementById('horasExtraId').value = id;
                document.getElementById('horasExtra').value = data.horas_extra;

                // Abrir el modal de horas extra
                $('#modalHorasExtra').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se encontraron datos de horas extra para este registro.',
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos de horas extra.');
        });
}

// Seleccionamos el formulario y el botón de guardar de horas extra
const formHorasExtra = document.querySelector('#formHorasExtra');
const botonGuardarHorasExtra = formHorasExtra.querySelector('button[type="submit"]');

botonGuardarHorasExtra.addEventListener('click', (e) => {
    e.preventDefault(); // Evita el envío inmediato del formulario

    // Abre el modal de confirmación
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Se asignarán las horas extra al detalle de asistencia.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, asignar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar los datos del formulario con fetch
            const formData = new FormData(formHorasExtra);
        
            fetch('/consultas_personal/guardar-horas-extra', {
                method: 'POST',
                headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json', 
                    },
                body: formData
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Guardado!',
                        text: data.success,
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    // Cerrar el modal
                    const modalHorasExtra = bootstrap.Modal.getInstance(document.getElementById('modalHorasExtra'));
                    modalHorasExtra.hide();

                    // Recargar la tabla
                    $('#table_detalles_personal').DataTable().ajax.reload();
                } else if (data.errors) {
                    // Manejar errores de validación
                    for (const field in data.errors) {
                        const input = formHorasExtra.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            input.nextElementSibling.textContent = data.errors[field][0];
                        }
                    }
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al asignar las horas extra.',
                });
            });
        }
    });
});



</script>
@endsection
