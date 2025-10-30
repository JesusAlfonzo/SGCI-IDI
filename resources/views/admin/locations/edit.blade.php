@extends('adminlte::page')

@section('title', 'Editar Ubicación')
@section('content_header')
    <h1>Editar Ubicación: {{ $location->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            
            <form action="{{ route('admin.locations.update', $location) }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- Campo Nombre --}}
                <div class="form-group">
                    <label for="name">Nombre de la Ubicación</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $location->name) }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <a href="{{ route('admin.locations.index') }}" class="btn btn-default">Cancelar</a>
                <button type="submit" class="btn btn-success">Actualizar Ubicación</button>
            </form>
        </div>
    </div>
@stop