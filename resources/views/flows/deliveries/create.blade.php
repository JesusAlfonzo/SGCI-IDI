@extends('adminlte::page')

@section('title', 'Registro de Entrega | Pendientes')
@section('content_header')
    <h1>Bandeja de Entregas Pendientes</h1>
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

            {{-- 1. Si no hay solicitudes pendientes, mostrar un mensaje --}}
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
                        {{-- La variable $requestList viene del DeliveryController@create --}}
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

                    <button type="submit" class="btn btn-success btn-lg mt-3">
                        <i class="fas fa-truck"></i> Registrar Entrega y Finalizar Solicitud
                    </button>
                </form>

                {{-- Opcional: Mostrar la lista de solicitudes aprobadas aquí mismo como una tabla para mayor detalle --}}
                <h4 class="mt-5">Detalles de Solicitudes Pendientes</h4>
                <p>Las siguientes solicitudes están listas para ser entregadas (Stock reservado).</p>
                <table class="table table-sm table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Solicitante</th>
                            <th>Propósito</th>
                            <th>Fecha Aprobación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requestList as $request)
                            <tr>
                                <td>{{ $request['code'] }}</td>
                                <td>{{ $request['requester'] }}</td>
                                <td>{{ \App\Models\RequestModel::find($request['id'])->purpose ?? 'Sin propósito' }}</td>
                                <td>{{ \App\Models\RequestModel::find($request['id'])->approval_date ? \App\Models\RequestModel::find($request['id'])->approval_date->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @endif
        </div>
    </div>
@stop
