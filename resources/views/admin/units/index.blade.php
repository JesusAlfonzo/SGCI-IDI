@extends('adminlte::page')

@section('title', 'Gestión de Unidades')
@section('content_header')
    <h1>Lista de Unidades de Medida</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.units.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Crear Nueva Unidad
            </a>
            <h3 class="card-title">Listado de Unidades</h3>
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
                    @foreach ($units as $unit)
                        <tr>
                            <td>{{ $unit->id }}</td>
                            <td>{{ $unit->name }}</td>
                            <td>{{ $unit->created_at->format('d/m/Y') }}</td>
                            <td>
                                {{-- Botón EDITAR --}}
                                <a href="{{ route('admin.units.edit', $unit) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                </a>
                                
                                {{-- Botón ELIMINAR --}}
                                <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar esta unidad?')">
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
            {{ $units->links() }} {{-- Paginación --}}
        </div>
    </div>
@stop