<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // Los IDs de compra y producto no deben cambiar.
            'purchase_id' => ['prohibited'],
            'product_id' => ['prohibited'],
            
            // Atributos que pueden ser modificados administrativamente
            'quantity_received' => ['sometimes', 'required', 'integer', 'min:1'],
            'unit_price' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'lot_number' => ['nullable', 'string', 'max:100'],
            'expiration_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}