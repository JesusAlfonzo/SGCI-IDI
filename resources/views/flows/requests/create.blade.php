@extends('adminlte::page')

@section('title', 'Crear Solicitud de Materiales')

@section('content_header')
    <h1>Crear Solicitud de Materiales (Salida de Stock)</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Información de la Solicitud</h3>
        </div>
        
        <form id="requestForm" action="{{ route('flows.requests.store') }}" method="POST">
            @csrf
            <div class="card-body">
                
                {{-- Mensajes de Error y Sesión --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6>Errores de Validación:</h6>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                <div class="row">
                    {{-- Campo: Fecha de Solicitud --}}
                    <div class="form-group col-md-4">
                        <label for="request_date">Fecha de Solicitud</label>
                        <input type="date" name="request_date" id="request_date" 
                               class="form-control @error('request_date') is-invalid @enderror" 
                               value="{{ old('request_date', date('Y-m-d')) }}" required>
                        @error('request_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Campo: Solicitado por --}}
                    <div class="form-group col-md-4">
                        <label>Solicitado Por (Usuario)</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                    </div>

                    {{-- Campo: Propósito / Razón --}}
                    <div class="form-group col-md-4">
                        <label for="purpose">Propósito / Razón</label>
                        <textarea name="purpose" id="purpose" class="form-control @error('purpose') is-invalid @enderror" rows="1" placeholder="Ej: Material para el Proyecto X">{{ old('purpose') }}</textarea>
                        @error('purpose') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <hr>

                {{-- TABLA DE DETALLES DE PRODUCTO --}}
                <h4 class="mb-3">Detalle de Materiales Solicitados</h4>
                <div class="table-responsive">
                    <table class="table table-bordered" id="product_details_table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Producto</th>
                                <th style="width: 15%;" class="text-center">Stock Actual</th>
                                <th style="width: 15%;">Unidad</th>
                                <th style="width: 15%;">Cantidad Solicitada</th>
                                <th style="width: 15%;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Fila PLantilla CLONADA (oculta al inicio) --}}
                            {{-- Esta fila usa el índice [0] para ser un placeholder seguro --}}
                            <tr id="detail-row-template" style="display: none;">
                                <td>
                                    {{-- **IMPORTANTE**: Estos campos están deshabilitados en JS al inicio y al submit --}}
                                    <select name="details[0][product_id]" class="form-control product-select" disabled> 
                                        <option value="">Seleccione Producto</option>
                                        @foreach ($products as $product)
                                            <option 
                                                value="{{ $product->id }}" 
                                                data-stock="{{ $product->stock_actual }}" 
                                                data-unit="{{ $product->unit->name ?? 'N/A' }}">
                                                {{ $product->name }} (Stock: {{ $product->stock_actual }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center"><span class="current-stock">-</span></td>
                                <td><span class="product-unit">N/A</span></td>
                                <td>
                                    <input type="number" name="details[0][quantity_requested]" class="form-control quantity-input" min="1" disabled>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-detail-row" style="display:none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right">
                                    <button type="button" class="btn btn-info btn-sm" id="add-detail-row">
                                        <i class="fas fa-plus"></i> Añadir Producto
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
            </div> {{-- /.card-body --}}
            
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Registrar Solicitud
                </button>
                <a href="{{ route('flows.requests.index') }}" class="btn btn-default">Cancelar</a>
            </div>
        </form>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar el contador en 0. Será incrementado a 1 para la primera fila clonada.
            let rowCounter = 0; 
            const $tableBody = $('#product_details_table tbody');
            const $templateRow = $('#detail-row-template');

            // 🔑 CORRECCIÓN CLAVE 1: Deshabilitar permanentemente los inputs de la plantilla base [0]
            // para que nunca se envíen datos vacíos de la fila oculta.
            $templateRow.find('select, input').prop('disabled', true);


            // 1. Función para añadir una nueva fila de detalle
            function addRow() {
                rowCounter++; // Ahora el primer índice válido es 1 (details[1])
                const newRow = $templateRow.clone(); 
                
                // Limpiar y actualizar atributos de la nueva fila
                newRow.removeAttr('id').attr('data-row-id', rowCounter);
                newRow.find('select, input').val(''); 
                newRow.find('.current-stock').text('-');
                newRow.find('.product-unit').text('N/A');
                
                // Mostrar la nueva fila y limpiar estilos
                newRow.show(); 
                
                // 🔑 CORRECCIÓN CLAVE 2: Asignar el nuevo índice al atributo 'name' y HABILITAR el input
                newRow.find('.product-select')
                    .attr('name', `details[${rowCounter}][product_id]`)
                    .prop('required', true)
                    .prop('disabled', false); // HABILITAR
                    
                newRow.find('.quantity-input')
                    .attr('name', `details[${rowCounter}][quantity_requested]`)
                    .prop('required', true)
                    .prop('disabled', false); // HABILITAR
                
                // Mostrar botón de eliminar y adjuntar el evento
                newRow.find('.remove-detail-row').show().on('click', function() {
                    $(this).closest('tr').remove();
                });
                
                $tableBody.append(newRow);
            }

            // 2. Inicialización: Solo añadir una fila inicial
            // El contador iniciará en 0, el primer addRow() lo pondrá en 1 y generará details[1]
            addRow(); 
            
            // 3. Manejador para añadir filas al hacer clic en el botón
            $('#add-detail-row').on('click', function(e) {
                e.preventDefault();
                addRow();
            });
            
            // 4. Manejador para el cambio de producto (actualizar Stock y Unidad)
            $tableBody.on('change', '.product-select', function() {
                const $selectedOption = $(this).find('option:selected');
                const $row = $(this).closest('tr');
                const stock = parseInt($selectedOption.data('stock'));
                const unit = $selectedOption.data('unit');

                $row.find('.current-stock').text(stock || '-');
                $row.find('.product-unit').text(unit || 'N/A');
                
                const $quantityInput = $row.find('.quantity-input');
                $quantityInput.val(1).attr('max', stock);

                // Si se selecciona un producto, la cantidad es requerida (aunque ya lo pusimos en addRow)
                if ($(this).val()) {
                    $quantityInput.prop('required', true);
                }
            });
            
            // 5. Manejador de la validación y ENVÍO
            $('#requestForm').on('submit', function(e) {
                // Ya no necesitamos deshabilitar la plantilla aquí, se hace al inicio.
                let hasError = false;
                
                // Validar que haya al menos una fila REAL de detalle (todas excepto la plantilla)
                if ($tableBody.find('tr:visible').length === 0) {
                    alert('Debe añadir al menos un producto a la solicitud.');
                    hasError = true;
                    e.preventDefault();
                }
                
                // Validación de stock (UX)
                $tableBody.find('tr:visible').each(function() {
                    const $select = $(this).find('.product-select');
                    const $input = $(this).find('.quantity-input');
                    
                    const stock = parseInt($select.find('option:selected').data('stock'));
                    const requested = parseInt($input.val());

                    if ($select.val() && requested > stock) {
                        alert(`Error: La cantidad solicitada (${requested}) excede el stock actual (${stock}) para el producto seleccionado.`);
                        $input.addClass('is-invalid');
                        hasError = true;
                        e.preventDefault();
                        return false; 
                    } else {
                        $input.removeClass('is-invalid');
                    }
                });

                if (hasError) {
                    e.preventDefault();
                    $tableBody.find('.is-invalid:first').focus();
                }
                
                // Si no hay errores, el formulario se envía.
            });
        });
    </script>
@stop
