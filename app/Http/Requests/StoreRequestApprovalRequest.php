<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequestApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        $statusOptions = ['Aprobada', 'Rechazada']; // Opciones comunes para el status.

        return [
            // request_id es único ya que la relación es 1:1.
            'request_id' => ['required', 'exists:requests,id', 'unique:request_approvals,request_id'],
            // FK al usuario que aprueba/rechaza.
            'approver_user_id' => ['required', 'exists:users,id'],
            
            'status' => ['required', 'string', Rule::in($statusOptions)],
            'approval_date' => ['required', 'date'], // Columna approval_date [cite: 6]
            'comment' => ['nullable', 'string', 'max:500'],
        ];
    }
}