@extends('adminlte::page')

@section('title', 'Crear Categoría')
@section('content_header')
    <h1>Crear Nueva Categoría</h1>
@stop

{{-- 🔑 CORRECCIÓN: Usamos @section('content') --}}
@section('content')
    <div class="card">
        <div class="card-body">
            
            {{-- El formulario apunta al método store del controlador --}}
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                
                {{-- Campo Nombre --}}
                <div class="form-group">
                    <label for="name">Nombre de la Categoría</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                {{-- Campo Descripción --}}
                <div class="form-group">
                    <label for="description">Descripción (Opcional)</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <a href="{{ route('admin.categories.index') }}" class="btn btn-default">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Categoría</button>
            </form>
        </div>
    </div>
@stop