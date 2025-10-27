<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Todos los roles logueados pueden crear solicitudes.
        return true; 
    }

    public function rules(): array
    {
        // Los estados de la solicitud (Ej. Pendiente, Aprobada, Rechazada, Entregada)
        $statusOptions = ['Pendiente', 'Aprobada', 'Rechazada', 'Entregada', 'Cancelada'];

        return [
            // requested_by_user_id (Quién hace la solicitud).
            'requested_by_user_id' => ['required', 'exists:users,id'],
            // delivery_location_id (Dónde se usará/entregará).
            'delivery_location_id' => ['required', 'exists:locations,id'],
            
            // Atributos de la solicitud
            'reason' => ['required', 'string', 'max:500'], // Razón de la solicitud.
            'expected_date' => ['required', 'date', 'after_or_equal:today'], // Fecha de entrega esperada.
            'status' => ['required', 'string', Rule::in($statusOptions)], // Estado inicial (debería ser 'Pendiente').
        ];
    }
}