@extends('layouts.layout')
@section('title-page')
    LISTADO DE PLATOS
@endsection

@section('registros-collapsed', '')
@section('registros-expanded', 'true')
@section('registros-show', 'show')
@section('platos-active', 'active')

@section('section-page')

@include('registros.platos.modals.modal_create_plato')
@include('registros.platos.modals.modal_edit_plato')
@include('registros.platos.modals.modal_composicion_plato')


<div class="card-style settings-card-1 mb-30">
    @csrf
    <div class="title mb-30 d-flex justify-content-between align-items-center">
      <h6>Platos <i class="fa-solid fa-user-tie" style="color: rgb(7, 45, 168);"></i>
      </h6>
      <button class="btn btn-primary" onclick="openMdlNuevoPlato()">
        <i class="fa-solid fa-plus"></i> NUEVO
      </button>
    </div>
    <div class="table-responsive">
        @include('registros.platos.tables.table_list_platos')
    </div>
</div>
<!-- end card -->
@endsection

<script>
    let dtPlatos   =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTablePlatos();
        iniciarSelect2();
        events();
    })

    function events(){
        eventsMdlCreatePlato();
        eventsMdlEditPlato();
        eventsMdlComposicion();
    }

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            allowClear: true,
        } );
    }

    function iniciarDataTablePlatos(){
        const urlGetPlatos = '{{ route('registros.platos.getPlatos') }}';

        dtPlatos  =   new DataTable('#table_platos',{
            serverSide: true,
            processing: true,
            pageLength: 50, 

            ajax: {
                url: urlGetPlatos,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'calorias', name: 'calorias' },
                { data: 'proteinas', name: 'proteinas' },
                { data: 'carbohidratos', name: 'carbohidratos' },
                { data: 'grasas', name: 'grasas' },
                { data: 'peso', name: 'peso' },
                { data: 'costo', name: 'costo' },
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlComposicion(${data.id})">
                                        <i class="fa-solid fa-pen-to-square"></i> Composición
                                    </a>
                                </li>
            
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlEditPlato(${data.id})">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="">
                                        <i class="fa-solid fa-trash"></i> Eliminar
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

</script>