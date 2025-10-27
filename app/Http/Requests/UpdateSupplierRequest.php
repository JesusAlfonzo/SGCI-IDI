<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        // Obtiene el ID del proveedor que se está actualizando
        $supplierId = $this->route('supplier'); 
        // El campo priority clasifica al proveedor (A, B, C, D).
        $priorityOptions = ['A', 'B', 'C', 'D'];

        return [
            // El nombre es opcional (sometimes), pero si se envía, debe ser único, excluyendo el ID actual.
            'name' => [
                'sometimes', 
                'required', 
                'string', 
                'max:255', 
                Rule::unique('suppliers')->ignore($supplierId),
            ],
            
            // Priority es opcional, pero si se envía, debe ser válido.
            'priority' => ['sometimes', 'required', 'string', Rule::in($priorityOptions)],
        ];
    }
}