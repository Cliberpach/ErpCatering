<form action="" id="formAsignarHorarioRegimen" method="POST">    
    @csrf
    <div class="mb-3">
    <label for="colaboradorId" class="form-label">Nombre del Trabajador</label>
    <input type="hidden" name="colaborador_id" value="{{ $colaborador->id }}">
    <input type="text" class="form-control" value="{{ $colaborador->nombre }}" readonly>
</div>


    <div class="mb-3">
        <label for="horario_id" class="form-label">Horario</label>
        <select name="horario_id" id="horario_id" class="form-control select2_form">
            <option value="">Seleccione un horario</option>
            @foreach($horarios as $horario)
            <option value="{{ $horario->id }}">{{ $horario->descripcion }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="regimen_id" class="form-label">Régimen</label>
        <select name="regimen_id" id="regimen_id" class="form-control select2_form">
            <option value="">Seleccione un régimen</option>
            @foreach($regimenes as $regimen)
            <option value="{{ $regimen->id }}">{{ $regimen->nombre }}</option>
            @endforeach
        </select>
    </div>
</form>
