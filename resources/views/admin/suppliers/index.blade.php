@extends('adminlte::page')

@section('title', 'Gestión de Proveedores')
@section('content_header')
    <h1>Lista de Proveedores</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Crear Nuevo Proveedor
            </a>
            <h3 class="card-title">Listado de Proveedores</h3>
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
                        <th>Prioridad</th>
                        <th>Contacto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td><span class="badge @if($supplier->priority == 'A') badge-success @elseif($supplier->priority == 'B') badge-warning @else badge-secondary @endif">{{ $supplier->priority }}</span></td>
                            <td>{{ $supplier->phone }} / {{ $supplier->email }}</td>
                            <td>
                                {{-- Botón EDITAR --}}
                                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                </a>
                                
                                {{-- Botón ELIMINAR --}}
                                <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar este proveedor?')">
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
            {{ $suppliers->links() }} {{-- Paginación --}}
        </div>
    </div>
@stop