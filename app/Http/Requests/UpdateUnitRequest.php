<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        // Obtiene el ID de la unidad que se está actualizando
        $unitId = $this->route('unit'); 

        return [
            // El nombre es opcional (sometimes), pero si se envía, debe ser único, excluyendo el ID actual.
            'name' => [
                'sometimes', 
                'required', 
                'string', 
                'max:100', 
                Rule::unique('units')->ignore($unitId),
            ],
        ];
    }
}