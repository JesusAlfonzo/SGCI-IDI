<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // No se debe cambiar el producto ni el proveedor al actualizar un registro histórico[cite: 8].
            'product_id' => ['prohibited'],
            'supplier_id' => ['prohibited'],
            
            // Solo se permite corregir el precio, la fecha o el flag de latest.
            'price' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'recorded_at' => ['sometimes', 'required', 'date'],
            'is_latest' => ['sometimes', 'boolean'],
        ];
    }
}