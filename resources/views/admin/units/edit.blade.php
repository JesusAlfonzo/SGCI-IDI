@extends('adminlte::page')

@section('title', 'Editar Unidad')
@section('content_header')
    <h1>Editar Unidad: {{ $unit->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            
            <form action="{{ route('admin.units.update', $unit) }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- Campo Nombre --}}
                <div class="form-group">
                    <label for="name">Nombre de la Unidad</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $unit->name) }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <a href="{{ route('admin.units.index') }}" class="btn btn-default">Cancelar</a>
                <button type="submit" class="btn btn-success">Actualizar Unidad</button>
            </form>
        </div>
    </div>
@stop