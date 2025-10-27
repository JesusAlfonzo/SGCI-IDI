<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKitUsageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // FK al kit que se está usando. kit_id referencia a la tabla kits.
            'kit_id' => ['required', 'exists:kits,product_id'], 
            // FK al usuario que usa el kit.
            'used_by_user_id' => ['required', 'exists:users,id'],
            
            'usage_date' => ['required', 'date'], // Columna usage_date[cite: 5].
            'purpose' => ['required', 'string', 'max:500'], // Campo de descripción del uso.
        ];
    }
}