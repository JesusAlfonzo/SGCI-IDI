<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo administradores y superiores deben poder crear maestros.
        return true; 
    }

    public function rules(): array
    {
        return [
            // El nombre (o abreviatura) debe ser requerido y único.
            'name' => ['required', 'string', 'max:100', 'unique:units'], 
        ];
    }
}