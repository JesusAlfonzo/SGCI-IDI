@extends('adminlte::page')

@section('title', 'Crear Unidad')
@section('content_header')
    <h1>Crear Nueva Unidad de Medida</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            
            <form action="{{ route('admin.units.store') }}" method="POST">
                @csrf
                
                {{-- Campo Nombre (Solo un campo 'name') --}}
                <div class="form-group">
                    <label for="name">Nombre de la Unidad (Ej. Caja, mL, Pza)</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <a href="{{ route('admin.units.index') }}" class="btn btn-default">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Unidad</button>
            </form>
        </div>
    </div>
@stop