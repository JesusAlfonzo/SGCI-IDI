<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
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
        return [
            // REGLAS PARA LA CABECERA (Purchase)
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'total_amount' => ['required', 'numeric', 'min:0'],

            // REGLAS PARA LOS DETALLES (Array Anidado)
            'details' => ['required', 'array', 'min:1'], 
            
            'details.*.product_id' => ['required', 'integer', 'exists:products,id'],
            // 🔑 CORRECCIÓN: Permitir 0 si es una entrada gratuita o cambia la inicialización JS
            'details.*.unit_cost' => ['required', 'numeric', 'min:0'], 
            'details.*.quantity' => ['required', 'integer', 'min:1'], 
        ];
    }
    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'details.required' => 'Debe agregar al menos un ítem a la compra.',
            'details.min' => 'La compra debe contener al menos un producto.',
            
            // Mensajes específicos para los campos de detalle
            'details.*.product_id.required' => 'El producto en la línea :attribute es obligatorio.',
            'details.*.product_id.exists' => 'El producto seleccionado en la línea :attribute no es válido.',
            'details.*.unit_cost.required' => 'El costo unitario en la línea :attribute es obligatorio.',
            'details.*.unit_cost.min' => 'El costo unitario en la línea :attribute debe ser mayor a cero.',
            'details.*.quantity.required' => 'La cantidad en la línea :attribute es obligatoria.',
            'details.*.quantity.min' => 'La cantidad en la línea :attribute debe ser de al menos 1.',
        ];
    }

    /**
     * Define los atributos que deben ser reemplazados en los mensajes de validación.
     * Esto hace que el error diga "Línea 1" en lugar de "details.0".
     */
    public function attributes(): array
    {
        // Esto toma details.0.product_id y lo convierte en "Línea 1 - Producto"
        $attributes = [];

        if ($this->has('details')) {
            foreach ($this->input('details') as $key => $detail) {
                $line = $key + 1;
                $attributes["details.{$key}.product_id"] = "Línea {$line} - Producto";
                $attributes["details.{$key}.unit_cost"] = "Línea {$line} - Costo Unitario";
                $attributes["details.{$key}.quantity"] = "Línea {$line} - Cantidad";
            }
        }

        return $attributes;
    }
}