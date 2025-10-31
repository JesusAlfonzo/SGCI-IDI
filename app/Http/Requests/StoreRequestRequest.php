<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // 🔑 Asegúrate de que esta línea esté presente

class StoreRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Todos los roles logueados pueden crear solicitudes.
        return true; 
    }

    public function rules(): array
    {
        // Los estados de la solicitud (Ej. Pendiente, Aprobada, Rechazada, Entregada, Cancelada)
        $statusOptions = ['PENDIENTE', 'APROBADA', 'RECHAZADA', 'ENTREGADA', 'CANCELADA'];

        return [
            // 🔑 CAMPOS DE CABECERA (Ajustados a lo que envía el formulario)
            'request_date' => ['required', 'date'],
            'purpose' => ['nullable', 'string', 'max:500'],
            
            // 🔑 REGLAS CLAVE PARA EL DETALLE (Matriz de productos)
            'details' => ['required', 'array', 'min:1'],
            'details.*.product_id' => ['required', 'exists:products,id'],
            'details.*.quantity_requested' => ['required', 'integer', 'min:1'],

            // Aunque estos campos no se envían por el formulario, si tu controlador los necesita
            // para la validación antes de inyectarlos, se pueden mantener o comentar:
            // 'requested_by_user_id' => ['required', 'exists:users,id'],
            // 'delivery_location_id' => ['required', 'exists:locations,id'],
            // 'reason' => ['required', 'string', 'max:500'], 
            // 'expected_date' => ['required', 'date', 'after_or_equal:today'], 
            // 'status' => ['required', 'string', Rule::in($statusOptions)], 
        ];
    }
    
    // ❌ QUITAR: El método prepareForValidation fue la causa de los errores P1013
    /*
    protected function prepareForValidation()
    {
         if (is_null($this->input('request_date'))) {
             $this->merge(['request_date' => date('Y-m-d')]);
         }
    }
    */
}