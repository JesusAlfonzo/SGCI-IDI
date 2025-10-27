<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // product_id es la clave foránea y actúa como clave primaria (1:1).
            'product_id' => [
                'required', 
                'integer', 
                'exists:products,id', 
                'unique:kits,product_id' // 1:1: un producto solo puede ser un kit una vez
            ],
            
            // total_usages es el potencial de uso total del kit[cite: 5].
            'total_usages' => ['required', 'integer', 'min:1'], // INTEGER[cite: 5].
        ];
    }
}