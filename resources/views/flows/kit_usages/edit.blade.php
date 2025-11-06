@extends('adminlte::page')

@section('title', 'Editar Kit: ' . $kit->product->name)

@section('content_header')
    <h1 class="m-0 text-dark">Editar Kit: {{ $kit->product->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- 1. Formulario de Datos Principales del Kit --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Datos Principales (Usos)</h3>
                </div>
                <form action="{{ route('inventory.kits.update', $kit) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label for="product_name">Producto Base (Solo lectura)</label>
                            <input type="text" class="form-control" value="{{ $kit->product->name }}" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="total_usages">Usos Totales Disponibles</label>
                            <input type="number" name="total_usages" id="total_usages" 
                                class="form-control @error('total_usages') is-invalid @enderror" 
                                value="{{ old('total_usages', $kit->total_usages) }}" min="0">
                            <small class="form-text text-muted">Esta es la cantidad de veces que el kit puede ser consumido.</small>
                            @error('total_usages')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Usos
                        </button>
                    </div>
                </form>
            </div>

            {{-- 2. Formulario de Gestión de Componentes --}}
            <div class="card card-info mt-4" id="component-manager">
                <div class="card-header">
                    <h3 class="card-title">Composición del Kit (Componentes)</h3>
                </div>
                <form action="{{ route('kits.sync_components', $kit) }}" method="POST">
                    @csrf
                    
                    <div class="card-body">
                        <table class="table table-bordered" id="components-table">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Componente (Producto)</th>
                                    <th style="width: 20%;">Cantidad Requerida por Kit</th>
                                    <th style="width: 10%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Componentes actuales cargados desde el controlador --}}
                                @foreach($currentComponents as $index => $component)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="components[{{ $index }}][product_id]" value="{{ $component->id }}">
                                            <input type="text" class="form-control" value="{{ $component->name }}" disabled>
                                        </td>
                                        <td>
                                            <input type="number" name="components[{{ $index }}][quantity]" 
                                                class="form-control" value="{{ $component->pivot->quantity }}" min="1" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-component">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right">
                                        <button type="button" class="btn btn-sm btn-secondary" id="add-component-btn">
                                            <i class="fas fa-plus"></i> Añadir Componente
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        {{-- ⚠️ Dropdown Oculto para clonar --}}
                        <select id="component-select-template" class="d-none">
                            <option value="">Seleccione un producto</option>
                            @foreach($componentProducts as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-sitemap"></i> Actualizar Composición
                        </button>
                        <a href="{{ route('inventory.kits.index') }}" class="btn btn-default">
                            Regresar
                        </a>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
@stop

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.querySelector('#components-table tbody');
        const addButton = document.getElementById('add-component-btn');
        const selectTemplate = document.getElementById('component-select-template');
        let componentIndex = {{ $currentComponents->count() }}; // Inicia el índice después de los componentes existentes

        addButton.addEventListener('click', function () {
            const newRow = document.createElement('tr');
            
            // Columna Producto (Dropdown)
            const selectCell = document.createElement('td');
            const select = selectTemplate.cloneNode(true);
            select.id = ''; // Limpiar el ID
            select.className = 'form-control';
            select.name = `components[${componentIndex}][product_id]`;
            select.required = true;
            selectCell.appendChild(select);
            newRow.appendChild(selectCell);

            // Columna Cantidad
            const quantityCell = document.createElement('td');
            const quantityInput = document.createElement('input');
            quantityInput.type = 'number';
            quantityInput.name = `components[${componentIndex}][quantity]`;
            quantityInput.className = 'form-control';
            quantityInput.value = 1;
            quantityInput.min = 1;
            quantityInput.required = true;
            quantityCell.appendChild(quantityInput);
            newRow.appendChild(quantityCell);

            // Columna Acción (Eliminar)
            const actionCell = document.createElement('td');
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-sm btn-danger remove-component';
            removeButton.innerHTML = '<i class="fas fa-trash"></i>';
            actionCell.appendChild(removeButton);
            newRow.appendChild(actionCell);

            tableBody.appendChild(newRow);
            componentIndex++;
        });

        // Delegación de eventos para eliminar filas
        tableBody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-component')) {
                e.target.closest('tr').remove();
            }
        });
        
        // Manejar la eliminación de filas preexistentes
        document.querySelectorAll('.remove-component').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('tr').remove();
            });
        });
    });
</script>
@endpush