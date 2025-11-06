@extends('adminlte::page')

@section('title', 'Crear Usuario')
@section('content_header')
    <h1 class="m-0 text-dark">Creación de Usuarios</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Redireccionando al Registro</h3>
                </div>
                <div class="card-body">
                    <p class="text-center">Serás redirigido al formulario de registro estándar de la aplicación.</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('register') }}" class="btn btn-info">Ir a Formulario de Registro</a>
                </div>
            </div>
        </div>
    </div>
@stop