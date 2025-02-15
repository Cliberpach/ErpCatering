@extends('layouts.layout')
@section('title-page')
    LISTADO DE SEDES
@endsection

@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('sede-active', 'active')

@section('section-page')
<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Sedes <i class="fa-solid fa-building"></i></h6>
      <button class="btn btn-primary" onclick="goToCrearSede()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="table-responsive">
        @include('registros.sedes.tables.table_list_sedes')
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
    let dtSede = null;

    document.addEventListener('DOMContentLoaded', ()=>{
        iniciarDataTable();
    });

    function iniciarDataTable(){
        const urlGetSede = '{{ route('registros.sedes.getSedes') }}';

        dtSede = new DataTable('#table_sedes', {
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetSede,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'direccion', name: 'direccion' },
                { data: 'encargado', name: 'encargado' },
                {
                    data: null,
                    render: function(data, type, row) {
                        const urlEdit = `{{ route('registros.sedes.edit', ':id') }}`.replace(':id', data.id);
                        const urlDelete = `{{ route('registros.sedes.destroy', ':id') }}`.replace(':id', data.id);
                        return `
                            <div class="btn-group dropstart">
                                <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-grip"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="${urlEdit}"><i class="fa-solid fa-pen-to-square"></i> Editar</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="eliminarSede(${data.id})"><i class="fa-solid fa-trash"></i> Eliminar</a></li>
                                </ul>
                            </div>
                        `;
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                },
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                emptyTable: "No hay datos disponibles en la tabla"
            }
        });
    }

    function goToCrearSede(){
        window.location.href = @json(route('registros.sedes.create'));
    }

    function eliminarSede(id){
        toastr.clear();
        let row = getRowById(dtSede, id);
        let message = `Desea eliminar la sede: ${row.nombre}`;

        Swal.fire({
            title: message,
            text: "Operación no reversible!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Cargando...', html: 'Eliminando sede...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                try {
                    let urlDeleteSede = `{{ route('registros.sedes.destroy', ':id') }}`.replace(':id', id);
                    const token = document.querySelector('input[name="_token"]').value;
                    const response = await fetch(urlDeleteSede, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } });
                    const res = await response.json();
                    if(res.success){ dtSede.draw(); toastr.success(res.message,'OPERACIÓN COMPLETADA'); }
                    else{ toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR SEDE'); }
                } catch (error) {
                    toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR SEDE');
                } finally {
                    Swal.close();
                }
            } else {
                Swal.fire({ title: "Operación cancelada", text: "No se realizaron acciones", icon: "error" });
            }
        });
    }
</script>
