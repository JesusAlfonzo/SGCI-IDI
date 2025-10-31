@extends('adminlte::page') {{-- Asumo que usas AdminLTE o una plantilla similar --}}

@section('title', 'Detalle de Solicitud ' . $materialRequest->request_code)

@section('content_header')
    <h1 class="m-0 text-dark">Detalle de Solicitud: {{ $materialRequest->request_code }}</h1>
@stop

@section('content')

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
                        <p><strong>Solicitante:</strong> {{ $materialRequest->requestedBy->name ?? 'Usuario Eliminado' }}</p>
                        <p><strong>Fecha de Solicitud:</strong> {{ $materialRequest->request_date->format('d/m/Y H:i') }}</p>
                        <p><strong>Fecha de Entrega Estimada:</strong> {{ $materialRequest->delivery_date ? $materialRequest->delivery_date->format('d/m/Y H:i') : 'Pendiente' }}</p>
                    </div>

                    {{-- Columna Derecha: Estado y Propósito --}}
                    <div class="col-md-6">
                        @php
                            $statusMap = [
                                'Pending' => ['text' => 'PENDIENTE', 'class' => 'warning'],
                                'Approved' => ['text' => 'APROBADA', 'class' => 'info'],
                                'Rejected' => ['text' => 'RECHAZADA', 'class' => 'danger'],
                                'Delivered' => ['text' => 'ENTREGADA', 'class' => 'success'],
                            ];
                            $statusInfo = $statusMap[$materialRequest->status] ?? ['text' => 'DESCONOCIDO', 'class' => 'secondary'];
                        @endphp
                        <p><strong>Estado:</strong> <span class="badge badge-{{ $statusInfo['class'] }}">{{ $statusInfo['text'] }}</span></p>
                        <p><strong>Propósito / Razón:</strong></p>
                        <textarea class="form-control" rows="4" readonly>{{ $materialRequest->purpose ?? 'Sin especificar.' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Materiales Solicitados ({{ $materialRequest->details->count() }} ítems)</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-right">Cantidad Solicitada</th>
                            <th>Unidad</th>
                            <th class="text-right">Stock Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materialRequest->details as $detail)
                        <tr>
                            <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                            <td class="text-right">{{ number_format($detail->quantity_requested, 0) }}</td>
                            <td>{{ $detail->product->unit->name ?? 'N/A' }}</td>
                            <td class="text-right text-{{ $detail->quantity_requested > $detail->product->stock_actual ? 'danger' : 'success' }}">
                                {{ number_format($detail->product->stock_actual ?? 0, 0) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">No hay detalles de productos para esta solicitud.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('flows.requests.index') }}" class="btn btn-default">Volver al listado</a>
                {{-- Botones de acción para administradores (Aprobar/Rechazar) --}}
                @can('manage-requests') {{-- Asumo un permiso para gestionar solicitudes --}}
                    @if ($materialRequest->status === 'Pending')
                        {{-- Aquí irían los botones para cambiar el estado --}}
                    @endif
                @endcan
            </div>
        </div>
    </div>
</div>

@stop
