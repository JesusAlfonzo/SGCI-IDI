@extends('adminlte::page')

@section('title', 'Mis Solicitudes de Materiales')
@section('content_header')
    <h1>Mis Solicitudes de Materiales</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('flows.requests.create') }}" class="btn btn-success float-right">
                <i class="fas fa-plus"></i> Crear Nueva Solicitud
            </a>
            <h3 class="card-title">Historial de Solicitudes</h3>
        </div>
        
        <div class="card-body">
            
            {{-- Mensajes de Session --}}
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
                        <th style="width: 10%;">Fecha Solicitud</th>
                        <th style="width: 25%;">Solicitado Por</th>
                        <th style="width: 15%;">Estado</th>
                        <th style="width: 15%;">Fecha Entrega</th>
                        <th style="width: 20%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr>
                            <td>
                                {{-- Enlace al detalle de la solicitud (asumimos flows.requests.show) --}}
                                <a href="{{ route('flows.requests.show', $request->id) }}" title="Ver Detalle">
                                    {{ $request->request_code }}
                                </a>
                            </td>
                            <td>{{ $request->request_date->format('d/m/Y') }}</td>
                            <td>{{ $request->requestedBy->name ?? 'Usuario Desconocido' }}</td>
                            <td><span class="badge badge-{{ $request->status == 'ENTREGADA' ? 'success' : ($request->status == 'APROBADA' ? 'info' : ($request->status == 'PENDIENTE' ? 'warning' : 'danger')) }}">{{ $request->status }}</span></td>
                            <td>{{ $request->delivery_date ? $request->delivery_date->format('d/m/Y') : 'Pendiente' }}</td>
                            <td>
                                {{-- Botón Ver Detalle --}}
                                <a href="{{ route('flows.requests.show', $request->id) }}" class="btn btn-xs btn-default text-info mx-1 shadow" title="Ver Detalles">
                                    <i class="fa fa-lg fa-fw fa-eye"></i>
                                </a>
                                {{-- Botón Editar (Solo si está pendiente o si el usuario puede editar) --}}
                                @if ($request->status == 'PENDIENTE' && Auth::id() == $request->requested_by_user_id)
                                <a href="{{ route('flows.requests.edit', $request->id) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay solicitudes registradas.</td>
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