<?php

namespace App\Http\Requests; 

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequestDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'request_id' => ['prohibited'],
            'product_id' => ['prohibited'],
            'quantity_requested' => ['sometimes', 'required', 'integer', 'min:1'],
        ];
    }
}