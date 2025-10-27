<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequestDeliveryDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Prohibir la modificación de todas las claves principales una vez entregado.
            'request_id' => ['prohibited'],
            'product_id' => ['prohibited'],
            'delivered_by_user_id' => ['prohibited'],
            'received_by_user_id' => ['prohibited'],
            
            // Solo se permite corregir la cantidad entregada o la fecha.
            'quantity_delivered' => ['sometimes', 'required', 'integer', 'min:1'],
            'delivery_date' => ['sometimes', 'required', 'date'],
        ];
    }
}