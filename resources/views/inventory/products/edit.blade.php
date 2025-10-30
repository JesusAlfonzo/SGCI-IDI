@extends('adminlte::page')

@section('title', 'Editar Producto')
@section('content_header')
    <h1>Editar Producto: {{ $product->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            
            <form action="{{ route('inventory.products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- Bloque de Claves Foráneas --}}
                <div class="row">
                    {{-- Categoría --}}
                    <div class="form-group col-md-4">
                        <label for="category_id">Categoría</label>
                        <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                            <option value="">Seleccione Categoría</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    {{-- Unidad de Medida --}}
                    <div class="form-group col-md-4">
                        <label for="unit_id">Unidad de Medida</label>
                        <select name="unit_id" class="form-control @error('unit_id') is-invalid @enderror" required>
                            <option value="">Seleccione Unidad</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    {{-- Ubicación --}}
                    <div class="form-group col-md-4">
                        <label for="location_id">Ubicación de Stock</label>
                        <select name="location_id" class="form-control @error('location_id') is-invalid @enderror" required>
                            <option value="">Seleccione Ubicación</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" {{ old('location_id', $product->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('location_id') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                </div>

                <hr>

                {{-- Bloque de Datos Principales --}}
                <div class="row">
                    {{-- SKU --}}
                    <div class="form-group col-md-4">
                        <label for="sku">SKU / Código de Barras (Opcional)</label>
                        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}">
                        @error('sku') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                    
                    {{-- Nombre --}}
                    <div class="form-group col-md-8">
                        <label for="name">Nombre del Producto</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required autofocus>
                        @error('name') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                </div>

                {{-- Bloque de Stock --}}
                <div class="row">
                    {{-- Stock Actual (Generalmente no se edita directamente en CRUD, pero lo incluiremos si se requiere) --}}
                    <div class="form-group col-md-4">
                        <label for="stock_actual">Stock Actual</label>
                        {{-- IMPORTANTE: El stock_actual normalmente solo se modifica por flujos (Compra, Venta), no manualmente. Se pone readonly para evitar cambios accidentales. --}}
                        <input type="number" name="stock_actual" class="form-control @error('stock_actual') is-invalid @enderror" value="{{ old('stock_actual', $product->stock_actual) }}" min="0" required readonly>
                        <small class="form-text text-muted">El stock se ajusta mediante los flujos de Entrada/Salida.</small>
                        @error('stock_actual') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    {{-- Stock Mínimo --}}
                    <div class="form-group col-md-4">
                        <label for="stock_minimo">Stock Mínimo (Alerta)</label>
                        <input type="number" name="stock_minimo" class="form-control @error('stock_minimo') is-invalid @enderror" value="{{ old('stock_minimo', $product->stock_minimo) }}" min="0" required>
                        @error('stock_minimo') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                    
                    {{-- Es Kit (Checkbox) --}}
                    <div class="form-group col-md-4">
                        <div class="custom-control custom-switch mt-4 pt-1">
                            <input type="checkbox" name="is_kit" class="custom-control-input" id="is_kit" value="1" {{ old('is_kit', $product->is_kit) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_kit">Este producto es un Kit</label>
                        </div>
                        @error('is_kit') <span class="text-danger"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                </div>

                {{-- Descripción --}}
                <div class="form-group">
                    <label for="description">Descripción / Notas</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $product->description) }}</textarea>
                    @error('description') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                </div>

                <a href="{{ route('inventory.products.index') }}" class="btn btn-default">Cancelar</a>
                <button type="submit" class="btn btn-success">Actualizar Producto</button>
            </form>
        </div>
    </div>
@stop