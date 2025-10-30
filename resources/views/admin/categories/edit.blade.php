@extends('adminlte::page')

@section('title', 'Editar Categoría')
@section('content_header')
    <h1>Editar Categoría: {{ $category->name }}</h1>
@stop

{{-- 🔑 CORRECCIÓN: Usamos @section('content') --}}
@section('content')
    <div class="card">
        <div class="card-body">
            
            {{-- El formulario apunta al método update del controlador --}}
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT') {{-- Se necesita el método PUT/PATCH para Resource --}}
                
                {{-- Campo Nombre --}}
                <div class="form-group">
                    <label for="name">Nombre de la Categoría</label>
                    {{-- Usamos old() para mantener el valor en caso de error de validación, sino usa el valor del modelo --}}
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $category->name) }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                {{-- Campo Descripción --}}
                <div class="form-group">
                    <label for="description">Descripción (Opcional)</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <a href="{{ route('admin.categories.index') }}" class="btn btn-default">Cancelar</a>
                <button type="submit" class="btn btn-success">Actualizar Categoría</button>
            </form>
        </div>
    </div>
@stop