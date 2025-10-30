@extends('adminlte::page')

@section('title', 'Inventario de Productos')
@section('content_header')
    <h1>Lista de Productos en Inventario</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('inventory.products.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Crear Nuevo Producto
            </a>
            <h3 class="card-title">Listado de Productos</h3>
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
                        <th>SKU</th>
                        <th>Nombre</th>
                        {{-- 🔑 NUEVA COLUMNA DESCRIPCIÓN --}}
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Ubicación</th>
                        <th>Stock Actual</th>
                        <th>Stock Mín.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $product->sku }}</td>
                            <td>
                                {{ $product->name }}
                                @if ($product->is_kit)
                                    <span class="badge badge-info">KIT</span>
                                @endif
                            </td>
                            {{-- 🔑 MOSTRAR DESCRIPCIÓN (limitando la longitud) --}}
                            <td>{{ Str::limit($product->description, 50, '...') }}</td>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                            <td>{{ $product->location->name ?? 'N/A' }}</td>
                            <td>
                                {{ $product->stock_actual }} 
                                <span class="badge badge-secondary">{{ $product->unit->name ?? '' }}</span>
                                @if ($product->stock_actual <= $product->stock_minimo)
                                    <i class="fas fa-exclamation-triangle text-danger ml-1" title="Stock bajo"></i>
                                @endif
                            </td>
                            <td>{{ $product->stock_minimo }}</td>
                            <td>
                                {{-- Botón EDITAR --}}
                                <a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                </a>
                                
                                {{-- Botón ELIMINAR --}}
                                <form action="{{ route('inventory.products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Eliminar" onclick="return confirm('ADVERTENCIA: ¿Está seguro de que desea eliminar este producto? Esto puede afectar el historial.')">
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
            {{ $products->links() }} {{-- Paginación --}}
        </div>
    </div>
@stop