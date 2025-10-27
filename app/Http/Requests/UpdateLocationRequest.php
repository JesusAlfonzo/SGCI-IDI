<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        // Obtiene el ID de la ubicación que se está actualizando
        $locationId = $this->route('location'); 

        return [
            // El nombre es opcional, pero si se envía, debe ser único, excluyendo el registro actual.
            'name' => [
                'sometimes', 
                'required', 
                'string', 
                'max:100', 
                Rule::unique('locations')->ignore($locationId),
            ],
        ];
    }
}