<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        $statusOptions = ['Pendiente', 'Aprobada', 'Rechazada', 'Entregada', 'Cancelada'];

        return [
            'requested_by_user_id' => ['sometimes', 'required', 'exists:users,id'],
            'delivery_location_id' => ['sometimes', 'required', 'exists:locations,id'],
            
            'reason' => ['sometimes', 'required', 'string', 'max:500'],
            'expected_date' => ['sometimes', 'required', 'date', 'after_or_equal:today'],
            
            // La actualización del estado solo puede ser hecha por roles de gestión/aprobación.
            'status' => ['sometimes', 'required', 'string', Rule::in($statusOptions)], 
        ];
    }
}