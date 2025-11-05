@extends('adminlte::page')

@section('title', 'Registro de Entrega | Pendientes')
@section('content_header')
    <h1 class="m-0 text-dark">Bandeja de Entregas Pendientes</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            {{-- Título que refleja la acción del Almacenista --}}
            <h3 class="card-title">Selecciona la Solicitud Aprobada para Registrar su Entrega</h3>
            {{-- Botón para regresar al historial --}}
            <div class="card-tools">
                <a href="{{ route('flows.deliveries.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-history"></i> Ver Historial de Entregas
                </a>
            </div>
        </div>
        
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Usamos $requestList del DeliveryController@create --}}
            @if ($requestList->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fas fa-check-circle"></i> ¡Felicidades! No hay solicitudes aprobadas pendientes de entrega.
                </div>
            @else
                {{-- 2. Formulario para seleccionar y registrar la entrega --}}
                <form action="{{ route('flows.deliveries.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="request_id">Seleccionar Solicitud Aprobada:</label>
                        {{-- USAMOS SELECT ESTÁNDAR (sin select2) --}}
                        <select name="request_id" id="request_id" class="form-control" required>
                            <option value="">-- Selecciona una Solicitud --</option>
                            @foreach ($requestList as $request)
                                <option value="{{ $request['id'] }}" {{ old('request_id') == $request['id'] ? 'selected' : '' }}>
                                    {{ $request['display'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('request_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes">Notas de Entrega (Opcional):</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                        @error('notes')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success btn-lg mt-3" 
                        onclick="return confirm('¿Está seguro de registrar la entrega para la solicitud seleccionada? Esto afectará el historial.');">
                        <i class="fas fa-truck"></i> Registrar Entrega y Finalizar Solicitud
                    </button>
                </form>

                <h4 class="mt-5">Detalles de Solicitudes Pendientes (Aprobadas)</h4>
                <p>Las siguientes solicitudes están listas para ser entregadas.</p>
                <table class="table table-sm table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Solicitante</th>
                            <th>Propósito</th>
                            <th>Fecha Aprobación</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Usamos $requestList para mostrar detalles abajo --}}
                        @foreach ($requestList as $request)
                            <tr>
                                <td>{{ $request['code'] }}</td>
                                <td>{{ $request['requester'] }}</td>
                                {{-- Usamos el helper find para obtener datos adicionales del RequestModel --}}
                                @php
                                    // Asumiendo que RequestModel se puede encontrar globalmente o se importa en el controlador
                                    $fullRequest = \App\Models\RequestModel::find($request['id']);
                                @endphp
                                <td>{{ \Illuminate\Support\Str::limit($fullRequest->purpose ?? 'N/A', 40) }}</td>
                                <td>{{ $fullRequest->approval_date ? $fullRequest->approval_date->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    {{-- Enlace para ver los detalles completos de la solicitud (Asumiendo que esta ruta existe) --}}
                                    <a href="{{ route('flows.requests.show', $fullRequest->id) }}" class="btn btn-xs btn-info">Ver Solicitud</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@stop