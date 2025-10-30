<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        // La instancia del proveedor se inyecta en el controlador
        $supplierId = $this->route('supplier')->id ?? null;

        return [
            // 🔑 CORRECCIÓN: Ignorar el ID actual para unicidad
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers', 'name')->ignore($supplierId)],
            'priority' => ['required', Rule::in(['A', 'B', 'C'])],

            // 🔑 CORRECCIÓN: Agregar los campos de Contacto e ignorar el ID actual
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')->ignore($supplierId)],
        ];
    }
    
    // Puedes reutilizar los mismos mensajes que en StoreSupplierRequest
}