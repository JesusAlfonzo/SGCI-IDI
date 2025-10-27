<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo administradores y superiores deben registrar entradas.
        return true; 
    }

    public function rules(): array
    {
        return [
            // purchase_code es único (para factura/OC)[cite: 5].
            'purchase_code' => ['required', 'string', 'max:100', 'unique:purchases'], 
            // purchase_date es DATE[cite: 5].
            'purchase_date' => ['required', 'date'],
            
            // Claves Foráneas
            // supplier_id (¿De quién se hizo la compra?) [cite: 9]
            'supplier_id' => ['required', 'exists:suppliers,id'], 
            // registered_by_user_id (¿Quién registró la compra?) [cite: 9]
            'registered_by_user_id' => ['required', 'exists:users,id'], 
        ];
    }
}