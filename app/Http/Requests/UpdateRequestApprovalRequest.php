<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequestApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statusOptions = ['Aprobada', 'Rechazada'];
        
        return [
            // Prohibidas las claves principales.
            'request_id' => ['prohibited'],
            'approver_user_id' => ['prohibited'], 
            
            // Solo se actualiza el status (si se revierte la decisión) y el comentario.
            'status' => ['sometimes', 'required', 'string', Rule::in($statusOptions)],
            'approval_date' => ['sometimes', 'required', 'date'],
            'comment' => ['nullable', 'string', 'max:500'],
        ];
    }
}