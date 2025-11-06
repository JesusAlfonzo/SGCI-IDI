@extends('adminlte::page')

@section('title', 'Definir Nuevo Kit')

@section('content_header')
    <h1 class="m-0 text-dark">Definir Nuevo Kit</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-8">
            
            {{-- Mensajes de feedback --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Selección de Producto Base y Usos Iniciales</h3>
                </div>
                
                {{-- Formulario para crear el Kit --}}
                <form action="{{ route('inventory.kits.store') }}" method="POST">
                    @csrf
                    <div class="card-body">

                        {{-- Campo Producto Base --}}
                        <div class="form-group">
                            <label for="product_id">Producto Base (Ítem a convertir en Kit) <span class="text-danger">*</span></label>
                            {{-- $products debe contener solo productos que NO son Kits --}}
                            <select name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror">
                                <option value="">Seleccione un Producto</option>
                                @foreach($products as $id => $name)
                                    <option value="{{ $id }}" {{ old('product_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Solo se muestran productos que aún no han sido definidos como Kits.</small>
                        </div>

                        {{-- Campo Usos Totales --}}
                        <div class="form-group">
                            <label for="total_usages">Usos Totales Iniciales <span class="text-danger">*</span></label>
                            <input type="number" name="total_usages" id="total_usages" 
                                class="form-control @error('total_usages') is-invalid @enderror" 
                                value="{{ old('total_usages', 1) }}" min="1" placeholder="Ej: 100">
                            @error('total_usages')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Cantidad de veces que el kit puede ser consumido.</small>
                        </div>
                        
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-magic"></i> Definir Kit
                        </button>
                        <a href="{{ route('inventory.kits.index') }}" class="btn btn-default">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="alert alert-info mt-3">
                Una vez creado, debe editar el Kit para definir su composición (qué productos individuales se consumen).
            </div>

        </div>
    </div>
@stop