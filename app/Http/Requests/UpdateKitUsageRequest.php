<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKitUsageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Prohibir la modificación de claves en un registro histórico.
            'kit_id' => ['prohibited'],
            'used_by_user_id' => ['prohibited'],
            'usage_date' => ['prohibited'],
            
            // Solo se permite actualizar la descripción o propósito.
            'purpose' => ['sometimes', 'required', 'string', 'max:500'],
        ];
    }
}