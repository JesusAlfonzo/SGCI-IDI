@extends('adminlte::page')

@section('title', 'Gestión de Aprobaciones')
@section('content_header')
    <h1>Solicitudes Pendientes de Aprobación</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Solicitudes con Estado PENDIENTE</h3>
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
                        <th style="width: 15%;">Estado Actual</th>
                        <th style="width: 30%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- 
                        NOTA: Aquí deberías iterar sobre $pendingRequests, que solo carga el controlador
                        MaterialRequestController@approvalsIndex (un método que deberemos crear).
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
                            <td><span class="badge badge-warning">{{ $request->status }}</span></td>
                            <td>
                                {{-- Botón Ver Detalle --}}
                                <a href="{{ route('flows.requests.show', $request->id) }}" class="btn btn-xs btn-default text-info mx-1 shadow" title="Ver Detalles">
                                    <i class="fa fa-lg fa-fw fa-eye"></i>
                                </a>
                                {{-- Botón de Acción Principal (Aprobar) --}}
                                <a href="{{ route('flows.approvals.edit', $request->id) }}" class="btn btn-xs btn-success mx-1 shadow" title="Aprobar Solicitud">
                                    <i class="fa fa-lg fa-fw fa-check"></i> Aprobar
                                </a>
                                {{-- Botón de Acción Secundaria (Rechazar) --}}
                                <button type="button" class="btn btn-xs btn-danger mx-1 shadow" title="Rechazar Solicitud">
                                    <i class="fa fa-lg fa-fw fa-times"></i> Rechazar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay solicitudes pendientes de aprobación.</td>
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