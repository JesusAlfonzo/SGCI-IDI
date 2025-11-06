@extends('adminlte::page')

@section('title', 'Registrar Uso de Kit')

@section('content_header')
    <h1 class="m-0 text-dark">Registrar Uso de Kit</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">

            {{-- Mensajes de feedback --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Formulario de Consumo</h3>
                </div>
                
                {{-- Formulario para registrar el uso --}}
                <form action="{{ route('flows.kit_usages.store') }}" method="POST">
                    @csrf
                    <div class="card-body">

                        {{-- Campo Kit a Usar --}}
                        <div class="form-group">
                            <label for="kit_id">Kit a Consumir <span class="text-danger">*</span></label>
                            <select name="kit_id" id="kit_id" class="form-control @error('kit_id') is-invalid @enderror">
                                <option value="">Seleccione un Kit</option>
                                @foreach($kits as $id => $name)
                                    <option value="{{ $id }}" {{ old('kit_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo Fecha de Uso --}}
                        <div class="form-group">
                            <label for="usage_date">Fecha de Uso <span class="text-danger">*</span></label>
                            <input type="date" name="usage_date" id="usage_date" class="form-control @error('usage_date') is-invalid @enderror" value="{{ old('usage_date', date('Y-m-d')) }}">
                            @error('usage_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo Propósito / Notas --}}
                        <div class="form-group">
                            <label for="purpose">Propósito del Uso / Notas <span class="text-danger">*</span></label>
                            <textarea name="purpose" id="purpose" class="form-control @error('purpose') is-invalid @enderror" rows="3" placeholder="Ej: Uso en laboratorio de control de calidad.">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Campo Oculto: Usuario que registra el uso (automático) --}}
                        {{-- Nota: El StoreKitUsageRequest valida que el used_by_user_id exista. --}}
                        <input type="hidden" name="used_by_user_id" value="{{ auth()->id() }}">

                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-box-open"></i> Registrar Consumo
                        </button>
                        <a href="{{ route('flows.kit_usages.index') }}" class="btn btn-default">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop