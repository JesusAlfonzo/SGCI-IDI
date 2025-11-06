@extends('adminlte::page')

@section('title', 'Historial de Uso de Kits')

@section('content_header')
    <h1 class="m-0 text-dark">Historial de Uso de Kits</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            
            {{-- Mensajes de feedback --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Registros de Consumo</h3>
                    <div class="card-tools">
                        {{-- Botón para ir al formulario de registro --}}
                        <a href="{{ route('flows.kit_usages.create') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-box-open"></i> Registrar Nuevo Uso
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Kit Consumido</th>
                                <th>Usuario Registrador</th>
                                <th>Fecha de Uso</th>
                                <th style="width: 30%;">Notas / Propósito</th>
                                <th>Registrado en</th>
                                <th style="width: 100px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usages as $usage)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{-- Accede al nombre del producto a través de la relación Kit --}}
                                        <span class="badge badge-info">{{ $usage->kit->product->name ?? 'Kit No Encontrado' }}</span>
                                    </td>
                                    <td>{{ $usage->usedBy->name ?? 'Usuario Desconocido' }}</td>
                                    <td>{{ $usage->usage_date ? \Carbon\Carbon::parse($usage->usage_date)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ Str::limit($usage->notes, 60) }}</td>
                                    <td>{{ $usage->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        {{-- Botones de Acción (Ver, Editar, Eliminar) --}}
                                        <div class="btn-group">
                                            <a href="{{ route('flows.kit_usages.show', $usage) }}" title="Ver Detalles" class="btn btn-xs btn-default">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- Solo permitir edición si el usuario tiene el permiso de auditoría --}}
                                            @can('kits_auditar_uso')
                                                <a href="{{ route('flows.kit_usages.edit', $usage) }}" title="Editar Notas" class="btn btn-xs btn-default">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{-- Formulario para eliminar (generalmente solo permitido para Super Admin/Auditor) --}}
                                                <form action="{{ route('flows.kit_usages.destroy', $usage) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este registro? Esto NO revierte el stock.');" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-danger" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay registros de uso de kits disponibles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer">
                    {{ $usages->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@stop