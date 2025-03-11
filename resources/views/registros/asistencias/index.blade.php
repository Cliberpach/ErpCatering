@extends('layouts.layout')
@section('title-page', 'MARCAR ASISTENCIA')

@section('asistencias-collapsed', '')
@section('asistencias-expanded', 'true')
@section('asistencias-show', 'show')
@section('asistencias-active', 'active')

@section('section-page')
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="text-center">
        <h3 class="text-uppercase fw-bold text-primary">{{ $proyectoNombre }}</h3>
        <h4 class="text-uppercase text-muted" id="fecha"></h4>
        <h1 class="display-3 fw-bold text-dark" id="clock"></h1>
        
        <div class="card shadow p-4 mt-4" style="max-width: 400px; margin: auto;">
            <h5 class="text-center mb-3">Ingrese su ID de Empleado</h5>

            <form action="{{ route('registros.asistencias.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <select class="form-select" name="tipo_registro" required>
                        <option value="entrada">Hora de Entrada</option>
                        <option value="salida">Hora de Salida</option>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="nro_documento" placeholder="Ingrese su DNI" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrar</button>
            </form>
        </div>
        
        @if(session('success'))
        <div class="alert alert-success mt-3">
            ✅ {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger mt-3">
            ❌ {{ session('error') }}
        </div>
        @endif
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const formattedTime = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('clock').textContent = formattedTime;
    }

    function updateDate() {
        const now = new Date();
        const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
        document.getElementById('fecha').textContent = now.toLocaleDateString('es-ES', options);
    }

    setInterval(updateClock, 1000);
    updateClock();
    updateDate();
</script>
@endsection
