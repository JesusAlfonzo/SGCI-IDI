<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        // Obtiene el ID del usuario que se está actualizando
        $userId = $this->route('user'); 

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            
            // El email es único, ignorando el usuario que se está editando.
            'email' => [
                'sometimes', 
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($userId),
            ], 
            
            // La contraseña no es requerida, pero si se proporciona, se debe confirmar.
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], 
            
            // Opcional: Si manejas roles.
            'role' => ['sometimes', 'required', 'string', 'exists:roles,name'], 
        ];
    }
}