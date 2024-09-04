<table class="table table-hover table-striped" id="table_usuarios_libres">
    <thead>
      <tr>
        <th scope="col"></th>
        <th scope="col">#</th>
        <th scope="col">USUARIO</th>
        <th scope="col">ROL</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($usuarios_libres as $usuario_libre)
            @php
                $checked = in_array($usuario_libre->usuario_id, $idsAsignados) ? 'checked' : '';
            @endphp
            <tr>
                <th>
                    <div class="form-check">
                        <input {{ $checked }} class="form-check-input chkUsuarioLibre" data-usuario-id="{{$usuario_libre->usuario_id}}" type="checkbox" value="" id="flexCheckDefault">
                    </div> 
                </th>
                <th>{{$usuario_libre->usuario_id}}</th>
                <td>{{$usuario_libre->usuario_nombre}}</td>
                <td>{{$usuario_libre->rol_nombre}}</td>
            </tr>
        @endforeach
    </tbody>
</table>