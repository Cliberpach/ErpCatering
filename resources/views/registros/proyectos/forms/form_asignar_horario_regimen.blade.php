<form id="formAsignarHorarioRegimen">
    @csrf
    <input type="hidden" name="colaborador_id" id="colaborador_id">

    <div class="mb-3">
        <label for="horario_id" class="form-label">Horario</label>
        <select name="horario_id" id="horario_id" class="form-control select2_form">
            <option value="">Seleccione un horario</option>
            @foreach($horarios as $horario)
                <option value="{{ $horario->id }}">{{ $horario->nombre }}</option>
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

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
