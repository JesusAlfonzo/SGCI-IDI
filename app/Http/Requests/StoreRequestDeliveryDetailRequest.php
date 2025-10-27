<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestDeliveryDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // FKs del registro de entrega
            'request_id' => ['required', 'exists:requests,id'],
            'product_id' => ['required', 'exists:products,id'],
            'delivered_by_user_id' => ['required', 'exists:users,id'], // Quién entrega 
            'received_by_user_id' => ['required', 'exists:users,id'], // Quién recibe 
            
            // Atributo principal
            'quantity_delivered' => ['required', 'integer', 'min:1'], // La cantidad entregada [cite: 6]
            'delivery_date' => ['required', 'date'],
        ];
    }
}