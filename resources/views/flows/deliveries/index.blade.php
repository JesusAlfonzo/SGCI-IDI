@extends('adminlte::page')

@section('title', 'Gestión de Entregas')
@section('content_header')
    <h1>Solicitudes Aprobadas Pendientes de Entrega</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Materiales por Entregar (Salida de Stock)</h3>
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
                        <th style="width: 25%;">Solicitado Por</th>
                        <th style="width: 15%;">Estado</th>
                        <th style="width: 30%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- 
                        NOTA: Aquí deberías iterar sobre $approvedRequests (solicitudes aprobadas),
                        que cargará el controlador (un nuevo DeliveriesController o un método en RequestController).
                    --}}
                    @forelse ($requests as $request)
                        <tr>
                            <td>
                                <a href="{{ route('flows.requests.show', $request->id) }}" title="Ver Detalle">
                                    {{ $request->request_code }}
                                </a>
                            </td>
                            <td>{{ $request->request_date->format('d/m/Y') }}</td>
                            <td>{{ $request->requestedBy->name ?? 'Desconocido' }}</td>
                            <td><span class="badge badge-info">{{ $request->status }}</span></td>
                            <td>
                                {{-- Botón Ver Detalle --}}
                                <a href="{{ route('flows.requests.show', $request->id) }}" class="btn btn-xs btn-default text-info mx-1 shadow" title="Ver Detalles">
                                    <i class="fa fa-lg fa-fw fa-eye"></i>
                                </a>
                                {{-- Botón de Acción Principal (Registrar Entrega) --}}
                                <a href="{{ route('flows.deliveries.edit', $request->id) }}" class="btn btn-xs btn-success mx-1 shadow" title="Registrar Entrega">
                                    <i class="fa fa-lg fa-fw fa-handshake"></i> Entregar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay solicitudes aprobadas pendientes de entrega.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer clearfix">
            {{ $requests->links() }} 
        </div>
    </div>
@stop