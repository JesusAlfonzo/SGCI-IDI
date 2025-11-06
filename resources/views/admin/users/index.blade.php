@extends('adminlte::page')

@section('title', 'Administración de Usuarios')
@section('content_header')
    <h1 class="m-0 text-dark">Gestión de Usuarios y Roles</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Cuentas del Sistema</h3>
                    <div class="card-tools">
                        {{-- 🛠️ NOTA: Redirigimos al formulario de registro de Laravel UI --}}
                        <a href="{{ route('admin.register') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Crear Nuevo Usuario
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Roles Asignados</th>
                                <th style="width: 150px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @forelse ($user->getRoleNames() as $role)
                                            <span class="badge badge-info">{{ $role }}</span>
                                        @empty
                                            <span class="badge badge-secondary">Sin Roles</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-xs btn-warning" title="Editar Roles">
                                            <i class="fas fa-user-tag"></i> Editar
                                        </a>
                                        {{-- Podrías añadir un botón de eliminación si lo deseas --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@stop