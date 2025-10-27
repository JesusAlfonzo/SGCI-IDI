<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        // $this->route('product') obtiene el modelo del producto que se está editando
        $productId = $this->route('product'); 
        
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            
            // sku debe ser único, excluyendo el producto actual.
            'sku' => [
                'sometimes',
                'required', 
                'string', 
                'max:50', 
                Rule::unique('products')->ignore($productId),
            ],
            
            'stock_minimo' => ['sometimes', 'required', 'integer', 'min:0'],
            'stock_actual' => ['nullable', 'integer', 'min:0'], 
            'is_kit' => ['boolean'],

            // Claves Foráneas
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'unit_id' => ['sometimes', 'required', 'exists:units,id'],
            'location_id' => ['sometimes', 'required', 'exists:locations,id'],
        ];
    }
}