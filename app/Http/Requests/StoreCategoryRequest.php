<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo administradores y superiores deben poder crear maestros.
        return true; 
    }

    public function rules(): array
    {
        return [
            // El nombre de la categoría debe ser único para evitar duplicados.
            'name' => ['required', 'string', 'max:100', 'unique:categories'], 
        ];
    }
}