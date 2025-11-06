@extends('adminlte::page')

@section('title', 'Maestro de Kits')

@section('content_header')
    <h1 class="m-0 text-dark">Inventario de Kits</h1>
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
                    <h3 class="card-title">Kits Definidos</h3>
                    <div class="card-tools">
                        {{-- Botón para ir al formulario de creación de Kits --}}
                        <a href="{{ route('inventory.kits.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-magic"></i> Definir Nuevo Kit
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Nombre del Kit (Producto)</th>
                                <th>Código/SKU</th>
                                <th>Usos Totales Disponibles</th>
                                <th>Componentes Asignados</th>
                                <th>Última Actualización</th>
                                <th style="width: 150px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kits as $kit)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $kit->product->name ?? 'Producto Eliminado' }}</span>
                                    </td>
                                    <td>{{ $kit->product->sku ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $kit->total_usages > 0 ? 'success' : 'danger' }}">
                                            {{ $kit->total_usages }}
                                        </span>
                                    </td>
                                    <td>
                                        {{-- Muestra cuántos componentes tiene, usando la relación components() --}}
                                        {{ $kit->components->count() }}
                                    </td>
                                    <td>{{ $kit->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        {{-- Botones de Acción (Editar y Eliminar) --}}
                                        <div class="btn-group">
                                            <a href="{{ route('inventory.kits.edit', $kit) }}" title="Editar Usos y Componentes" class="btn btn-xs btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            {{-- Formulario para eliminar --}}
                                            <form action="{{ route('inventory.kits.destroy', $kit) }}" method="POST" onsubmit="return confirm('ADVERTENCIA: ¿Está seguro de eliminar la definición de este Kit?');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger" title="Eliminar Kit">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay productos definidos como Kits.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer">
                    {{ $kits->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@stop