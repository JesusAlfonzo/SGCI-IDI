@extends('adminlte::page')

@section('title', 'Crear Ubicación')
@section('content_header')
    <h1>Crear Nueva Ubicación</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            
            <form action="{{ route('admin.locations.store') }}" method="POST">
                @csrf
                
                {{-- Campo Nombre --}}
                <div class="form-group">
                    <label for="name">Nombre de la Ubicación (Ej. Estante 1, Almacén Frío)</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <a href="{{ route('admin.locations.index') }}" class="btn btn-default">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Ubicación</button>
            </form>
        </div>
    </div>
@stop