<table class="table table-hover table-striped" id="table_maquinarias_libres">
    <thead>
      <tr>
        <th scope="col"></th>
        <th scope="col">MAQUINARIA</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($maquinarias_libres as $maquinaria_libre)
            @php
                $checked = in_array($maquinaria_libre->maquinaria_id, $idsAsignados) ? 'checked' : '';
            @endphp
            <tr>
                <th>
                    <div class="form-check">
                        <input {{ $checked }} class="form-check-input chkMaquinariaLibre" data-maquinaria-id="{{$maquinaria_libre->maquinaria_id}}" type="checkbox" value="" id="flexCheckDefault">
                    </div> 
                </th>
                <td>{{$maquinaria_libre->maquinaria_nombre}}</td>
            </tr>
        @endforeach
    </tbody>
</table>