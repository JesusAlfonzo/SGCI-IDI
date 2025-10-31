@extends('adminlte::page')

@section('title', 'Detalle de Compra #' . $purchase->purchase_code)
@section('content_header')
    <h1>Detalle de Compra <span class="text-primary">{{ $purchase->purchase_code }}</span></h1>
@stop

@section('content')
    <div class="row">

        {{-- COLUMNA PRINCIPAL (Datos de Cabecera) --}}
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Información General</h3>
                    <div class="card-tools">
                        <a href="{{ route('flows.purchases.index') }}" class="btn btn-sm btn-default">
                            <i class="fas fa-arrow-left"></i> Volver a Compras
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        {{-- BLOQUE IZQUIERDO --}}
                        <div class="col-md-6">
                            <p><strong>Proveedor:</strong> {{ $purchase->supplier->name }}</p>
                            <p><strong>Fecha de Compra:</strong>
                                {{ \Carbon\Carbon::parse($purchase->purchase_date)->isoFormat('D MMM YYYY') }}</p>
                            <p><strong>N° de Factura/Referencia:</strong> {{ $purchase->invoice_number ?? 'N/A' }}</p>
                        </div>

                        {{-- BLOQUE DERECHO --}}
                        <div class="col-md-6">
                            <p><strong>Código de Compra:</strong> <span
                                    class="badge badge-success">{{ $purchase->purchase_code }}</span></p>
                            <p><strong>Registrado por:</strong> {{ $purchase->registeredBy->name ?? 'Usuario Desconocido' }}
                            </p>
                            <p><strong>Fecha de Registro:</strong>
                                {{ $purchase->created_at->isoFormat('D MMM YYYY, h:mm a') }}</p>
                        </div>
                    </div>
                    <h4 class="mt-4">Total General: <span
                            class="text-danger">{{ number_format($purchase->total_amount, 2) }}</span></h4>
                </div>
            </div>
        </div>

        {{-- COLUMNA DE DETALLES (Productos) --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Productos Adquiridos (Detalle)</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-valign-middle">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Unidad</th>
                                    <th class="text-right">Costo Unitario</th>
                                    <th class="text-right">Cantidad</th>
                                    <th class="text-right">Subtotal Detalle</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchase->details as $detail)
                                    <tr>
                                        <td>{{ $detail->product->name }}</td>
                                        <td>{{ $detail->product->unit->name ?? 'N/A' }}</td>
                                        {{-- Usamos unit_purchase_price, ya que confirmamos que existe en BD --}}
                                        <td class="text-right">{{ number_format($detail->unit_purchase_price, 4) }}</td>
                                        <td class="text-right">{{ $detail->quantity }}</td>
                                        <td class="text-right">
                                            <strong>{{ number_format($detail->total_detail, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>TOTAL COMPRA:</strong></td>
                                    <td class="text-right"><strong>{{ number_format($purchase->total_amount, 2) }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
