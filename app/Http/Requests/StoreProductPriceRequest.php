<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // FK al producto [cite: 8]
            'product_id' => ['required', 'exists:products,id'],
            // FK al proveedor [cite: 8]
            'supplier_id' => ['required', 'exists:suppliers,id'],
            
            // Atributos de precio [cite: 5]
            'price' => ['required', 'numeric', 'min:0.01'],
            'recorded_at' => ['required', 'date'],
            'is_latest' => ['boolean'], // Es un BOOLEAN [cite: 5]
        ];
    }
}