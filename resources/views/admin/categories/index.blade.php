@extends('adminlte::page')

@section('title', 'Gestión de Categorías')
@section('content_header')
    <h1>Lista de Categorías</h1>
@stop

{{-- 🔑 CORRECCIÓN: Usamos @section('content') --}}
@section('content') 
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Crear Nueva Categoría
            </a>
            <h3 class="card-title">Listado de Categorías</h3>
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
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->created_at->format('d/m/Y') }}</td>
                            <td>
                                {{-- Botón EDITAR --}}
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                </a>
                                
                                {{-- Botón ELIMINAR (usando un formulario para método DELETE) --}}
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar esta categoría?')">
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
            {{ $categories->links() }} {{-- Paginación --}}
        </div>
    </div>
@stop