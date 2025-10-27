<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'request_id' => ['required', 'exists:requests,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity_requested' => ['required', 'integer', 'min:1'],
        ];
    }
}