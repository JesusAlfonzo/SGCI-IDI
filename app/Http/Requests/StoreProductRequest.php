<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Devolvemos true para ejecutar la validación (se manejará por roles en el Controller)
        return true; 
    }

    public function rules(): array
    {
        return [
            // Atributos obligatorios
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', 'unique:products'], // sku es único[cite: 5].
            'stock_minimo' => ['required', 'integer', 'min:0'], // Stock mínimo requerido[cite: 5].
            
            // Claves Foráneas (FKs) 
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'location_id' => ['required', 'exists:locations,id'],

            // Atributos opcionales o con valores por defecto
            'stock_actual' => ['nullable', 'integer', 'min:0'], // Es un INTEGER [cite: 5], el sistema lo actualiza[cite: 5].
            'is_kit' => ['boolean'], // Es un BOOLEAN[cite: 5].
        ];
    }
}