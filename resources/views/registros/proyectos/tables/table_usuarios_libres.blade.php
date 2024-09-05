<table class="table table-hover table-striped" id="table_usuarios_libres">
    <thead>
      <tr>
        <th scope="col"></th>
        <th scope="col">COLABORADOR</th>
        <th scope="col" style="text-align: start;">N° DOC</th>
        <th scope="col">CARGO</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($colaboradores_libres as $colaborador_libre)
            @php
                $checked = in_array($colaborador_libre->colaborador_id, $idsAsignados) ? 'checked' : '';
            @endphp
            <tr>
                <th>
                    <div class="form-check">
                        <input {{ $checked }} class="form-check-input chkColaboradorLibre" data-usuario-id="{{$colaborador_libre->colaborador_id}}" type="checkbox" value="" id="flexCheckDefault">
                    </div> 
                </th>
                <td>{{$colaborador_libre->colaborador_nombre}}</td>
                <td style="text-align: start;">{{$colaborador_libre->colaborador_nro_documento}}</td>
                <td>{{$colaborador_libre->cargo_nombre}}</td>
            </tr>
        @endforeach
    </tbody>
</table>