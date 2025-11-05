@extends('adminlte::page')

@section('title', 'Detalle de Entrega | ' . $delivery->request_code)
@section('content_header')
    <h1 class="m-0 text-dark">Detalle de Entrega: {{ $delivery->request_code }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title text-white">Información General de la Entrega</h3>
                    <div class="card-tools">
                        <a href="{{ route('flows.deliveries.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i> Volver al Historial
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Datos de Solicitud</h4>
                            <p><strong>Código:</strong> {{ $delivery->request_code }}</p>
                            <p><strong>Estado:</strong> <span class="badge badge-success">{{ $delivery->status }}</span></p>
                            <p><strong>Solicitante:</strong> {{ $delivery->requestedBy->name ?? 'N/A' }}</p>
                            <p><strong>Fecha de Solicitud:</strong> {{ $delivery->request_date->format('d/m/Y H:i:s') }}</p>
                            <p><strong>Propósito:</strong> {{ $delivery->purpose }}</p>
                            <p><strong>Aprobado Por:</strong> {{ $delivery->approvedBy->name ?? 'N/A' }}</p>
                            <p><strong>Fecha Aprobación:</strong> {{ $delivery->approval_date ? $delivery->approval_date->format('d/m/Y H:i:s') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h4>Datos de Entrega</h4>
                            <p><strong>Fecha de Entrega:</strong> {{ $delivery->delivery_date ? $delivery->delivery_date->format('d/m/Y H:i:s') : 'N/A' }}</p>
                            <p><strong>Entregado Por (Almacén):</strong> {{ $delivery->warehouseStaff->name ?? 'N/A' }}</p>
                            
                            @php
                                // Buscamos la nota de entrega del primer detalle, asumiendo que es la misma para toda la entrega
                                $deliveryNotes = $delivery->deliveryDetails->first()->delivery_notes ?? 'Sin notas registradas.';
                            @endphp
                            <p><strong>Notas de Entrega:</strong> {{ $deliveryNotes }}</p>
                        </div>
                    </div>
                    
                    <hr>

                    <h4 class="mt-4 mb-3">Materiales Entregados (Detalles de la Entrega)</h4>
                    
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad Solicitada</th>
                                <th>Cantidad Entregada</th>
                                <th>Unidad de Medida</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Iteramos sobre los detalles de la entrega (RequestDeliveryDetail) --}}
                            @forelse ($delivery->deliveryDetails as $detail)
                                <tr>
                                    <td>{{ $detail->product->name ?? 'Producto Eliminado' }}</td>
                                    <td>
                                        {{-- Intentamos obtener la cantidad original solicitada desde los detalles de la solicitud --}}
                                        @php
                                            $originalDetail = $delivery->details->where('product_id', $detail->product_id)->first();
                                        @endphp
                                        {{ number_format($originalDetail->quantity_requested ?? 'N/A', 0) }}
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ number_format($detail->quantity_delivered, 0) }}</span>
                                    </td>
                                    <td>{{ $detail->product->unit_of_measure ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No se encontraron detalles de entrega para esta solicitud.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop