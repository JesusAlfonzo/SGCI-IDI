<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // total_usages es el contador que puede necesitar ajustes administrativos.
            'total_usages' => ['sometimes', 'required', 'integer', 'min:1'],
            
            // El product_id no se actualiza en la relación 1:1 una vez creado.
            'product_id' => ['prohibited'], // Opcional: para evitar su inclusión accidental en el update
        ];
    }
}