@extends('adminlte::page')

@section('title', 'Editar Usuario | ' . $user->name)
@section('content_header')
    <h1 class="m-0 text-dark">Editar Usuario: {{ $user->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Datos y Roles</h3>
                </div>
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        
                        {{-- Campo Nombre --}}
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        {{-- Campo Email --}}
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        {{-- Asignación de Roles --}}
                        <div class="form-group">
                            <label for="roles">Asignar Roles (Spatie)</label>
                            <select name="roles[]" id="roles" class="form-control select2 @error('roles') is-invalid @enderror" multiple="multiple">
                                @foreach($roles as $id => $name)
                                    <option value="{{ $id }}" {{ in_array($id, $userRoles) ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('roles')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

{{-- Script para inicializar Select2 (si lo tienes activo en AdminLTE) --}}
@push('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Selecciona uno o más roles",
                allowClear: true
            });
        });
    </script>
@endpush