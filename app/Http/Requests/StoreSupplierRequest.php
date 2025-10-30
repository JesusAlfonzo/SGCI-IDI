<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Necesario para la regla ENUM

class StoreSupplierRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true; // Asume que si llega aquí, ya pasó el middleware 'auth'
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        return [
            // Campos que SÍ se estaban guardando (Ejemplo de reglas)
            'name' => ['required', 'string', 'max:255', 'unique:suppliers,name'],
            'priority' => ['required', Rule::in(['A', 'B', 'C'])], // Ajusta A, B, C a tus valores ENUM

            // 🔑 CORRECCIÓN: Agregar los campos de Contacto
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255', 'unique:suppliers,email'],
            
            // Agrega aquí cualquier otro campo que tenga tu tabla suppliers (ej. address)
        ];
    }

    /**
     * Mensajes personalizados para las reglas de validación.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Este nombre de proveedor ya existe.',
            'priority.in' => 'La prioridad seleccionada no es válida.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado en otro proveedor.',
        ];
    }
}