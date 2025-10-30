@extends('adminlte::page')

@section('title', 'Editar Proveedor')
@section('content_header')
    <h1>Editar Proveedor: {{ $supplier->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            
            <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    {{-- Campo Nombre --}}
                    <div class="form-group col-md-6">
                        <label for="name">Nombre / Razón Social</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $supplier->name) }}" required autofocus>
                        @error('name') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    {{-- Campo Prioridad (Select) --}}
                    <div class="form-group col-md-6">
                        <label for="priority">Prioridad (Clasificación)</label>
                        <select name="priority" class="form-control @error('priority') is-invalid @enderror" required>
                            <option value="">Seleccione Prioridad</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority }}" {{ old('priority', $supplier->priority) == $priority ? 'selected' : '' }}>{{ $priority }}</option>
                            @endforeach
                        </select>
                        @error('priority') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Campo Persona de Contacto --}}
                    <div class="form-group col-md-4">
                        <label for="contact_person">Persona de Contacto</label>
                        <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $supplier->contact_person) }}">
                        @error('contact_person') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                    
                    {{-- Campo Teléfono --}}
                    <div class="form-group col-md-4">
                        <label for="phone">Teléfono</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $supplier->phone) }}">
                        @error('phone') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    {{-- Campo Email --}}
                    <div class="form-group col-md-4">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $supplier->email) }}">
                        @error('email') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                </div>

                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-default">Cancelar</a>
                <button type="submit" class="btn btn-success">Actualizar Proveedor</button>
            </form>
        </div>
    </div>
@stop