@extends('adminlte::page')

@section('title', 'Registrar Compra')
@section('content_header')
    <h1>Registrar Nueva Compra (Entrada de Stock)</h1>
@stop

@section('content')

    {{-- 🔑 BLOQUE DE MENSAJES DE SESIÓN (Success/Error del controlador) --}}
    {{-- Si el error viene del 'catch' del controlador, se mostrará aquí --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{-- Muestra el mensaje de error completo enviado desde el controlador (DB Error) --}}
            {{ session('error') }}
        </div>
    @endif

    {{-- BLOQUE DE ERRORES DE VALIDACIÓN ($errors->any()): Muestra los errores de FormRequest --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> ¡Error de Validación!</h5>
            Se encontraron errores que impiden el registro. Por favor, revise los siguientes puntos:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <form action="{{ route('flows.purchases.store') }}" method="POST" id="purchaseForm">
                @csrf

                {{-- Bloque de Cabecera de Compra --}}
                <div class="row mb-4">
                    {{-- Proveedor --}}
                    <div class="form-group col-md-4">
                        <label for="supplier_id">Proveedor</label>
                        <select name="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror" required>
                            <option value="">Seleccione Proveedor</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    {{-- Fecha de Compra --}}
                    <div class="form-group col-md-4">
                        <label for="purchase_date">Fecha de Compra</label>
                        <input type="date" name="purchase_date"
                            class="form-control @error('purchase_date') is-invalid @enderror"
                            value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                        @error('purchase_date')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    {{-- Número de Factura --}}
                    <div class="form-group col-md-4">
                        <label for="invoice_number">Número de Factura (Opcional)</label>
                        <input type="text" name="invoice_number"
                            class="form-control @error('invoice_number') is-invalid @enderror"
                            value="{{ old('invoice_number') }}">
                        @error('invoice_number')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <h3>Detalles de la Compra</h3>
                <hr>

                {{-- Tabla para los Detalles de la Compra --}}
                <div class="table-responsive">
                    <table class="table table-striped" id="purchaseDetailsTable">
                        <thead>
                            <tr>
                                <th style="width: 40%">Producto</th>
                                <th style="width: 15%">Costo Unit.</th>
                                <th style="width: 15%">Cantidad</th>
                                <th style="width: 15%">Subtotal</th>
                                <th style="width: 10%">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Lógica para persistir los ítems si la validación falla (old('details')) --}}
                            @if (old('details'))
                                @foreach (old('details') as $index => $detail)
                                    <tr>
                                        <td>
                                            <select name="details[{{ $index }}][product_id]"
                                                class="form-control product-select" required>
                                                <option value="">Seleccione Producto</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        data-unit="{{ $product->unit->name ?? 'N/A' }}"
                                                        {{ old("details.$index.product_id") == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }} ({{ $product->unit->name ?? 'N/A' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        {{-- Importante: min="0.00" --}}
                                        <td><input type="number" name="details[{{ $index }}][unit_cost]"
                                                class="form-control unit-cost" step="0.0001" min="0.00" required
                                                value="{{ old("details.$index.unit_cost", 0.0) }}"></td>
                                        <td><input type="number" name="details[{{ $index }}][quantity]"
                                                class="form-control quantity" min="1" required
                                                value="{{ old("details.$index.quantity", 1) }}"></td>
                                        <td><input type="text" class="form-control subtotal" value="0.00" readonly>
                                        </td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-row"><i
                                                    class="fas fa-trash"></i></button></td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>TOTAL DE LA COMPRA:</strong></td>
                                <td>
                                    <input type="text" id="totalDisplay" class="form-control"
                                        value="{{ old('total_amount', 0.0) }}" readonly>
                                    {{-- Campo oculto para enviar el total al controlador --}}
                                    <input type="hidden" name="total_amount" id="totalAmountInput"
                                        value="{{ old('total_amount', 0.0) }}">
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-info" id="addRowButton"><i class="fas fa-plus"></i> Añadir
                        Ítem</button>
                    <div>
                        <a href="{{ route('flows.purchases.index') }}" class="btn btn-default">Cancelar</a>
                        <button type="submit" class="btn btn-success">Registrar Compra</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            var rowCount = $('#purchaseDetailsTable tbody tr').length;

            // 1. Plantilla de Fila
            var productOptions = '';
            @foreach ($products as $product)
                productOptions +=
                    `<option value="{{ $product->id }}" data-unit="{{ $product->unit->name ?? 'N/A' }}">{{ $product->name }} ({{ $product->unit->name ?? 'N/A' }})</option>`;
            @endforeach

            var templateRow = `
                <tr>
                    <td>
                        <select name="details[INDEX][product_id]" class="form-control product-select" required>
                            <option value="">Seleccione Producto</option>
                            ${productOptions}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="details[INDEX][unit_cost]" class="form-control unit-cost" step="0.0001" min="0.00" required value="0.00">
                    </td>
                    <td>
                        <input type="number" name="details[INDEX][quantity]" class="form-control quantity" min="1" required value="1">
                    </td>
                    <td>
                        <input type="text" class="form-control subtotal" value="0.00" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;

            // 2. Función de Cálculo
            function calculateTotal() {
                var total = 0;
                $('#purchaseDetailsTable tbody tr').each(function() {
                    var cost = parseFloat($(this).find('.unit-cost').val()) || 0;
                    var qty = parseInt($(this).find('.quantity').val()) || 0;
                    var subtotal = cost * qty;

                    $(this).find('.subtotal').val(subtotal.toFixed(2));
                    total += subtotal;
                });

                $('#totalDisplay').val(total.toFixed(2));
                $('#totalAmountInput').val(total.toFixed(2));
            }

            // 3. Añadir Fila
            $('#addRowButton').click(function() {
                var indexToUse = rowCount++;

                var newRowHtml = templateRow.replace(/INDEX/g, indexToUse);
                var newRow = $(newRowHtml);

                $('#purchaseDetailsTable tbody').append(newRow);

                // Asignar eventos y calcular total
                newRow.find('.unit-cost, .quantity').on('input', calculateTotal);
                calculateTotal();
            });

            // 4. Eliminar Fila (Delegación de Eventos)
            $('#purchaseDetailsTable').on('click', '.remove-row', function() {
                if ($('#purchaseDetailsTable tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotal();
                } else {
                    alert('Debe haber al menos un ítem en la compra.');
                }
            });

            // 5. Inicialización: Si hay filas persistentes, asignar eventos y calcular. Si no, agregar una.
            if (rowCount === 0) {
                $('#addRowButton').click();
            } else {
                $('#purchaseDetailsTable tbody tr').find('.unit-cost, .quantity').on('input', calculateTotal);
                calculateTotal();
            }
        });
    </script>
@stop
