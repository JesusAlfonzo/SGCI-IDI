<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo roles que pueden registrar entradas.
        return true; 
    }

    public function rules(): array
    {
        return [
            // FK al encabezado de la compra.
            'purchase_id' => ['required', 'exists:purchases,id'],
            // FK al producto.
            'product_id' => ['required', 'exists:products,id'],
            
            // Atributos de detalle
            'quantity_received' => ['required', 'integer', 'min:1'], // La cantidad recibida.
            'unit_price' => ['required', 'numeric', 'min:0.01'], // El precio de la unidad comprada.
            'lot_number' => ['nullable', 'string', 'max:100'], // Número de lote.
            'expiration_date' => ['nullable', 'date', 'after_or_equal:today'], // Fecha de vencimiento.
        ];
    }
}