@extends('adminlte::page')

@section('title', 'Aprobación de Solicitudes')

@section('content_header')
    <h1 class="m-0 text-dark">📝 Solicitudes Pendientes de Aprobación</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Solicitudes Pendientes (Status: Pending)</h3>
                </div>
                <div class="card-body">
                    
                    {{-- Mensajes de Sesión --}}
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Fecha Solicitud</th>
                                <th>Solicitado Por</th>
                                <th>Propósito</th>
                                <th style="width: 280px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingRequests as $request)
                                <tr>
                                    <td>
                                        {{ $request->request_code }}
                                    </td>
                                    <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                    <td>{{ $request->requestedBy->name ?? 'Usuario Desconocido' }}</td>
                                    <td>{{ Str::limit($request->purpose, 50) }}</td>
                                    <td>
                                        <form action="{{ route('flows.requests.approve', $request->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de APROBAR la solicitud {{ $request->request_code }}? Se descontará el stock inmediatamente.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Aprobar
                                            </button>
                                        </form>

                                        <form action="{{ route('flows.requests.reject', $request->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de RECHAZAR la solicitud {{ $request->request_code }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                        </form>

                                        <a href="{{ route('flows.requests.show', $request->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay solicitudes pendientes de aprobación.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    {{-- Paginación --}}
                    <div class="mt-3">
                        {{ $pendingRequests->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop