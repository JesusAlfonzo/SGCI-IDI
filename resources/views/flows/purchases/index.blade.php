@extends('adminlte::page')

@section('title', 'Registro de Compras')
@section('content_header')
    <h1>Registro de Compras (Entradas)</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            {{-- RUTA CORREGIDA: flows.purchases.create --}}
            <a href="{{ route('flows.purchases.create') }}" class="btn btn-success float-right">
                <i class="fas fa-plus"></i> Registrar Nueva Compra
            </a>
            <h3 class="card-title">Historial de Compras</h3>
        </div>
        <div class="card-body">
            
            {{-- Mensajes de Session --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha Compra</th>
                        <th>Proveedor</th>
                        <th># Factura</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->id }}</td>
                            <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $purchase->invoice_number ?? 'N/A' }}</td>
                            <td>${{ number_format($purchase->total_amount, 2) }}</td>
                            <td>
                                {{-- Botón SHOW para ver detalles --}}
                                {{-- NOTA: Este botón no está funcional sin la ruta 'show' --}}
                                <a href="#" class="btn btn-xs btn-default text-info mx-1 shadow" title="Ver Detalles">
                                    <i class="fa fa-lg fa-fw fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="card-footer clearfix">
            {{ $purchases->links() }} 
        </div>
    </div>
@stop