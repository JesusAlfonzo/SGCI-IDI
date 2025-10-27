<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        // Obtiene el ID de la compra que se está actualizando
        $purchaseId = $this->route('purchase'); 

        return [
            // purchase_code debe ser único, excluyendo el registro actual.
            'purchase_code' => [
                'sometimes', 
                'required', 
                'string', 
                'max:100', 
                Rule::unique('purchases')->ignore($purchaseId),
            ], 
            
            // Campos opcionales para actualizar
            'purchase_date' => ['sometimes', 'required', 'date'],
            'supplier_id' => ['sometimes', 'required', 'exists:suppliers,id'], 
            'registered_by_user_id' => ['sometimes', 'required', 'exists:users,id'], 
        ];
    }
}