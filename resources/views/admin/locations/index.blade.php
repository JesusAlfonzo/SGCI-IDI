@extends('adminlte::page')

@section('title', 'Gestión de Ubicaciones')
@section('content_header')
    <h1>Lista de Ubicaciones de Almacenamiento</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.locations.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Crear Nueva Ubicación
            </a>
            <h3 class="card-title">Listado de Ubicaciones</h3>
        </div>
        <div class="card-body">
            
            {{-- Mensajes de Session --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($locations as $location)
                        <tr>
                            <td>{{ $location->id }}</td>
                            <td>{{ $location->name }}</td>
                            <td>{{ $location->created_at->format('d/m/Y') }}</td>
                            <td>
                                {{-- Botón EDITAR --}}
                                <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                </a>
                                
                                {{-- Botón ELIMINAR --}}
                                <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar esta ubicación?')">
                                        <i class="fa fa-lg fa-fw fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="card-footer clearfix">
            {{ $locations->links() }} {{-- Paginación --}}
        </div>
    </div>
@stop