<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo administradores y superiores deben poder gestionar proveedores.
        return true; 
    }

    public function rules(): array
    {
        // El campo priority clasifica al proveedor (AB, C, D)[cite: 1].
        $priorityOptions = ['A', 'B', 'C', 'D'];

        return [
            // El nombre debe ser requerido y único en la tabla suppliers.
            'name' => ['required', 'string', 'max:255', 'unique:suppliers'], 
            
            // Priority es un ENUM, lo hacemos requerido y validamos que esté en las opciones.
            'priority' => ['required', 'string', Rule::in($priorityOptions)],
        ];
    }
}