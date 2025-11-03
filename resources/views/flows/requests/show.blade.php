@extends('adminlte::page') {{-- Usamos tu estructura original --}}

@section('title', 'Detalle de Solicitud ' . $materialRequest->request_code)

@section('content_header')
    <h1 class="m-0 text-dark">Detalle de Solicitud: {{ $materialRequest->request_code }}</h1>
@stop

@section('content')

{{-- Mensajes de Sesión (Éxito o Error) --}}
<div class="row">
    <div class="col-12">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>
</div>

{{-- Definición del Mapa de Estados (Traído de tu código original) --}}
@php
    // Mantengo esta definición local para asegurar que la vista tiene el estado correcto para el badge
    $statusMap = [
        'Pending' => ['text' => 'PENDIENTE', 'class' => 'warning'],
        'Approved' => ['text' => 'APROBADA', 'class' => 'info'],
        'Rejected' => ['text' => 'RECHAZADA', 'class' => 'danger'],
        'Delivered' => ['text' => 'ENTREGADA', 'class' => 'success'],
    ];
    // NOTA: El controlador también calcula $statusInfo, pero usar la definición local es robusto.
    $statusInfo = $statusMap[$materialRequest->status] ?? ['text' => 'DESCONOCIDO', 'class' => 'secondary'];
@endphp


{{-- TARJETA DE ACCIONES (Solo visible si el estado es 'Pending' y se tiene permiso) --}}
@can('manage-requests') {{-- Usamos el permiso que tenías definido --}}
    @if ($materialRequest->status === 'Pending')
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Flujo de Aprobación</h3>
            </div>
            <div class="card-body text-center">
                <p class="text-bold">Esta solicitud requiere su aprobación para descontar el stock.</p>
                
                <div class="d-flex justify-content-center mt-3">
                    
                    {{-- Botón de APROBAR --}}
                    <form 
                        action="{{ route('flows.requests.approve', $materialRequest) }}" 
                        method="POST" 
                        style="display:inline;" 
                        {{-- CRÍTICO: Reemplazar confirm() nativo por un modal personalizado --}}
                        {{-- onsubmit="return confirm('¿Está seguro de que desea APROBAR esta solicitud y descontar el stock? Esta acción es irreversible.');" --}}
                        onsubmit="return confirmApprove(this);"
                    >
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success btn-lg mx-2">
                            <i class="fas fa-check"></i> Aprobar Solicitud
                        </button>
                    </form>

                    {{-- Botón de RECHAZAR --}}
                    <form 
                        action="{{ route('flows.requests.reject', $materialRequest) }}" 
                        method="POST" 
                        style="display:inline;" 
                        {{-- CRÍTICO: Reemplazar confirm() nativo por un modal personalizado --}}
                        {{-- onsubmit="return confirm('¿Está seguro de que desea RECHAZAR esta solicitud?');" --}}
                        onsubmit="return confirmReject(this);"
                    >
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-danger btn-lg mx-2">
                            <i class="fas fa-times"></i> Rechazar Solicitud
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endcan


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información General</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Columna Izquierda: Datos de la Solicitud --}}
                    <div class="col-md-6">
                        <p><strong>Código de Solicitud:</strong> {{ $materialRequest->request_code }}</p>
                        {{-- Usamos tu relación 'requestedBy' --}}
                        <p><strong>Solicitante:</strong> {{ $materialRequest->requestedBy->name ?? 'Usuario Eliminado' }}</p>
                        {{-- La columna request_date debe ser un objeto Carbon gracias al Modelo --}}
                        <p><strong>Fecha de Solicitud:</strong> {{ $materialRequest->request_date?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                        <p><strong>Fecha de Entrega Estimada:</strong> {{ $materialRequest->delivery_date?->format('d/m/Y') ?? 'Pendiente' }}</p>
                    </div>

                    {{-- Columna Derecha: Estado y Propósito --}}
                    <div class="col-md-6">
                        <p><strong>Estado:</strong> <span class="badge badge-{{ $statusInfo['class'] }}">{{ $statusInfo['text'] }}</span></p>
                        <p><strong>Propósito / Razón:</strong></p>
                        <textarea class="form-control" rows="4" readonly>{{ $materialRequest->purpose ?? 'Sin especificar.' }}</textarea>
                    </div>
                </div>
                
                {{-- Información de Aprobación (Si está Aprobada o Rechazada) --}}
                @if ($materialRequest->status === 'Approved' || $materialRequest->status === 'Rejected')
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Relación 'approvedBy' --}}
                            <strong>Aprobador/Rechazador:</strong> {{ $materialRequest->approvedBy->name ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Fecha de Acción:</strong> {{ $materialRequest->approval_date?->format('d/m/Y H:i') ?? 'N/A' }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-info"> {{-- Añadí color para que se vea diferente --}}
            <div class="card-header">
                <h3 class="card-title">Materiales Solicitados ({{ $materialRequest->details->count() }} ítems)</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 50%">Producto</th>
                            <th class="text-right">Cantidad Solicitada</th>
                            <th>Unidad</th>
                            <th class="text-right">Stock Actual</th>
                            <th class="text-center">Estado Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Usando la relación details() del modelo --}}
                        @forelse ($materialRequest->details as $detail)
                        <tr>
                            <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                            <td class="text-right">{{ number_format($detail->quantity_requested, 0) }}</td>
                            <td>{{ $detail->product->unit->name ?? 'N/A' }}</td>
                            {{-- Se asegura de acceder al stock y poner color si es insuficiente --}}
                            <td class="text-right text-{{ ($detail->product->stock_actual ?? 0) < $detail->quantity_requested ? 'danger' : 'success' }}">
                                {{ number_format($detail->product->stock_actual ?? 0, 0) }}
                            </td>
                            <td class="text-center">
                                @if ($materialRequest->status === 'Approved')
                                    <span class="badge badge-success">Entregado: {{ $detail->quantity_delivered ?? $detail->quantity_requested }}</span>
                                @elseif ($materialRequest->status === 'Rejected')
                                    <span class="badge badge-danger">Rechazado</span>
                                @else
                                    <span class="badge badge-warning">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">No hay detalles de productos para esta solicitud.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('flows.requests.index') }}" class="btn btn-default">Volver al listado</a>
            </div>
        </div>
    </div>
</div>

@stop

{{-- Script para reemplazar confirm() con una advertencia en consola (idealmente sería un modal) --}}
@section('js')
<script>
    function confirmApprove(form) {
        console.warn("ADVERTENCIA: En un entorno de producción, use un modal de Bootstrap/AdminLTE para esta confirmación, no la función confirm() nativa.");
        // Simular el confirm() para fines de prueba, aunque se debe reemplazar con un modal
        if (window.confirm('¿Está seguro de que desea APROBAR esta solicitud y descontar el stock? Esta acción es irreversible.')) {
             return true;
        } else {
             return false;
        }
    }

    function confirmReject(form) {
        console.warn("ADVERTENCIA: En un entorno de producción, use un modal de Bootstrap/AdminLTE para esta confirmación, no la función confirm() nativa.");
        // Simular el confirm() para fines de prueba, aunque se debe reemplazar con un modal
        if (window.confirm('¿Está seguro de que desea RECHAZAR esta solicitud?')) {
             return true;
        } else {
             return false;
        }
    }
</script>
@stop
