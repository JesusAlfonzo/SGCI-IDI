@extends('adminlte::page')

@section('title', 'Historial de Entregas Finalizadas')
@section('content_header')
    <h1 class="m-0 text-dark">Historial de Entregas Registradas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Solicitudes Entregadas y Finalizadas</h3>
            {{-- Botón para ir a la bandeja de solicitudes pendientes de entrega --}}
            <div class="card-tools">
                <a href="{{ route('flows.deliveries.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-truck"></i> Bandeja de Entregas Pendientes
                </a>
            </div>
        </div>
        
        <div class="card-body">
            
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 15%;">Código</th>
                        <th style="width: 15%;">Fecha Solicitud</th>
                        <th style="width: 20%;">Solicitado Por</th>
                        <th style="width: 20%;">Fecha Entrega</th>
                        <th style="width: 15%;">Personal Almacén</th>
                        <th style="width: 15%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- La variable $deliveries viene del DeliveryController@index --}}
                    @forelse ($deliveries as $request)
                        <tr>
                            <td>
                                {{-- ENLACE AL SHOW DE ENTREGAS --}}
                                <a href="{{ route('flows.deliveries.show', $request->id) }}" title="Ver Detalle de Entrega">
                                    {{ $request->request_code }}
                                </a>
                            </td>
                            <td>{{ $request->request_date->format('d/m/Y') }}</td>
                            <td>{{ $request->requestedBy->name ?? 'Desconocido' }}</td>
                            <td>{{ $request->delivery_date ? $request->delivery_date->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $request->warehouseStaff->name ?? 'N/A' }}</td>
                            
                            <td>
                                <a href="{{ route('flows.deliveries.show', $request->id) }}" class="btn btn-xs btn-primary mx-1 shadow" title="Ver Detalles de Entrega">
                                    <i class="fa fa-lg fa-fw fa-eye"></i> Detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay entregas finalizadas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer clearfix">
            {{-- Usar $deliveries para la paginación --}}
            {{ $deliveries->links() }} 
        </div>
    </div>
@stop