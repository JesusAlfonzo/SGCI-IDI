<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo administradores o super administradores deberían poder crear usuarios.
        return true; 
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // Email debe ser único.
            
            // Requerimos y confirmamos la contraseña para la creación.
            'password' => ['required', 'string', 'min:8', 'confirmed'], 
            
            // Opcional: Si manejas roles directamente en el request.
            'role' => ['required', 'string', 'exists:roles,name'], 
        ];
    }
}